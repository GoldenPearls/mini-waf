<?php

namespace App\Controllers;

/**
 * WafMiddleware
 * 들어오는 HTTP 요청을 검사해서 공격 패턴을 탐지하는 핵심 클래스
 * SQL Injection, XSS, Path Traversal 세 가지 공격 유형을 탐지함
 */
class WafMiddleware
{
    /**
     * SQL Injection 탐지 패턴 목록
     * UNION SELECT, DROP TABLE 등 DB를 공격하는 패턴들
     */
    private static array $sqlPatterns = [
        '/(\bUNION\b.*\bSELECT\b)/i',   // UNION SELECT로 DB 데이터 탈취 시도
        '/(\bSELECT\b.*\bFROM\b)/i',    // SELECT FROM으로 테이블 조회 시도
        '/(\bDROP\b.*\bTABLE\b)/i',     // DROP TABLE로 테이블 삭제 시도
        '/(\bINSERT\b.*\bINTO\b)/i',    // INSERT INTO로 데이터 삽입 시도
        '/(\bDELETE\b.*\bFROM\b)/i',    // DELETE FROM으로 데이터 삭제 시도
        '/\'.*--/',                      // '-- 로 SQL 주석 처리해서 뒷부분 무력화
        '/\bOR\b\s+[\'\"]?\d+[\'\"]?\s*=\s*[\'\"]?\d+[\'\"]?/i',  // OR 1=1 같은 항상 참인 조건
        '/\bAND\b\s+[\'\"]?\d+[\'\"]?\s*=\s*[\'\"]?\d+[\'\"]?/i', // AND 1=1 같은 항상 참인 조건
        '/;\s*(DROP|ALTER|CREATE|TRUNCATE)/i', // 세미콜론으로 쿼리 이어붙여 추가 공격
        '/\bEXEC\b\s*\(/i',             // EXEC()로 저장 프로시저 실행 시도
        '/\bSLEEP\s*\(/i',             // SLEEP()으로 시간 기반 blind SQL injection
        '/\bWAITFOR\b/i',              // WAITFOR로 MSSQL 시간 지연 공격
    ];

    /**
     * XSS(Cross-Site Scripting) 탐지 패턴 목록
     * 브라우저에서 악성 스크립트를 실행시키는 패턴들
     */
    private static array $xssPatterns = [
        '/<script[^>]*>.*?<\/script>/is',              // <script>...</script> 직접 삽입
        '/javascript\s*:/i',                            // javascript: 프로토콜로 스크립트 실행
        '/on\w+\s*=\s*["\'][^"\']*["\']/i',            // onclick= onload= 등 이벤트 핸들러
        '/<\s*img[^>]+src\s*=\s*["\'][^"\']*javascript:/i', // 이미지 src에 javascript: 삽입
        '/eval\s*\(/i',                                 // eval()로 문자열을 코드로 실행
        '/expression\s*\(/i',                           // CSS expression()으로 스크립트 실행
        '/<\s*iframe/i',                                // iframe으로 외부 악성 페이지 삽입
        '/<\s*object/i',                                // object 태그로 악성 플러그인 실행
        '/document\.(cookie|write|location)/i',         // 쿠키 탈취 또는 페이지 조작
        '/window\.(location|open)/i',                   // 강제 리다이렉트 또는 팝업
    ];

    /**
     * Path Traversal 탐지 패턴 목록
     * 서버의 민감한 파일에 접근하려는 패턴들
     */
    private static array $pathPatterns = [
        '/\.\.[\/\\\\]/',              // ../  또는 ..\  로 상위 디렉토리 이동
        '/\.\.[\/\\\\].*\.\.[\/\\\\]/i', // ../../  반복으로 루트까지 올라가기
        '/%2e%2e[%2f%5c]/i',           // URL 인코딩된 ../  우회 시도
        '/etc\/passwd/i',              // 리눅스 계정 정보 파일 직접 접근
        '/windows\/system32/i',        // 윈도우 시스템 폴더 직접 접근
    ];

    /**
     * 단일 입력값을 검사해서 공격 여부 반환
     * 공격 탐지 시 ['type' => '공격유형', 'payload' => '입력값'] 반환
     * 정상이면 null 반환
     */
    public static function inspect(string $input): ?array
    {
        // SQL Injection 패턴 순서대로 검사
        foreach (self::$sqlPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return ['type' => 'SQL_INJECTION', 'payload' => $input];
            }
        }

        // XSS 패턴 순서대로 검사
        foreach (self::$xssPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return ['type' => 'XSS', 'payload' => $input];
            }
        }

        // Path Traversal 패턴 순서대로 검사
        foreach (self::$pathPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return ['type' => 'PATH_TRAVERSAL', 'payload' => $input];
            }
        }

        // 모든 패턴 통과 = 정상 요청
        return null;
    }

    /**
     * 현재 HTTP 요청의 GET/POST 파라미터와 URI를 전부 스캔
     * 하나라도 공격 패턴 발견되면 즉시 반환
     */
    public static function scanRequest(): ?array
    {
        // GET과 POST 파라미터를 합쳐서 전부 검사
        $inputs = array_merge($_GET, $_POST);

        foreach ($inputs as $value) {
            if (is_string($value)) {
                $result = self::inspect($value);
                if ($result !== null) {
                    return $result; // 공격 발견 즉시 반환
                }
            }
        }

        // URI 자체도 검사 (URL 인코딩 디코딩 후 검사)
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $decoded = urldecode($uri);
        return self::inspect($decoded);
    }
}