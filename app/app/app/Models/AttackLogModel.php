<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * AttackLogModel
 * attack_logs 테이블과 연결되는 모델
 * 공격 탐지 내역을 PostgreSQL에 저장하고 조회함
 */
class AttackLogModel extends Model
{
    // 연결할 테이블 이름
    protected $table      = 'attack_logs';
    // 기본키 컬럼명
    protected $primaryKey = 'id';

    // insert/update 허용할 컬럼 목록 (보안상 명시적으로 지정)
    protected $allowedFields = [
        'attack_type', 'payload', 'ip_address', 'uri', 'blocked'
    ];

    // created_at 자동 기록 활성화
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // updated_at은 사용 안 함

    /**
     * 공격 유형별 탐지 건수 통계 반환
     * 대시보드 상단 카드에 표시됨
     */
    public function getStats(): array
    {
        return $this->select('attack_type, COUNT(*) as count')
                    ->groupBy('attack_type')        // 유형별로 묶어서
                    ->orderBy('count', 'DESC')       // 많은 순으로 정렬
                    ->findAll();
    }

    /**
     * 최근 탐지 로그 N개 반환
     * 대시보드 테이블에 표시됨
     */
    public function getRecent(int $limit = 20): array
    {
        return $this->orderBy('created_at', 'DESC') // 최신순 정렬
                    ->limit($limit)
                    ->findAll();
    }
}