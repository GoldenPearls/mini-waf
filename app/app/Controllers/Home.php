<?php

namespace App\Controllers;

use App\Models\AttackLogModel;

/**
 * Home 컨트롤러
 * 메인 페이지와 공격 시뮬레이션 요청을 처리
 */
class Home extends BaseController
{
    /**
     * 메인 페이지 — 공격 테스트 폼 보여주기
     */
    public function index(): string
    {
        return view('home');
    }

    /**
     * 공격 시뮬레이션 처리
     * 1. WAF로 요청 검사
     * 2. 공격이면 DB에 로그 저장 후 차단 페이지
     * 3. 정상이면 안전 메시지 출력
     */
    public function testAttack(): string
    {
        // WAF 미들웨어로 현재 요청 전체 스캔
        $result = WafMiddleware::scanRequest();

        if ($result !== null) {
            // 공격 탐지 — DB에 로그 기록
            $logModel = new AttackLogModel();
            $logModel->insert([
                'attack_type' => $result['type'],             // 공격 유형 (SQL_INJECTION 등)
                'payload'     => $result['payload'],           // 실제 공격 내용
                'ip_address'  => $this->request->getIPAddress(), // 공격자 IP
                'uri'         => $this->request->getUri()->getPath(), // 요청 경로
                'blocked'     => true,                        // 차단 여부
            ]);

            // 차단 페이지로 이동
            return view('blocked', [
                'attackType' => $result['type'],
                'payload'    => $result['payload'],
            ]);
        }

        // 정상 요청 — XSS 방지를 위해 htmlspecialchars 적용 후 출력
        $query = $this->request->getPost('query');
        return view('home', ['message' => '✅ 안전한 요청입니다: ' . htmlspecialchars($query)]);
    }
}