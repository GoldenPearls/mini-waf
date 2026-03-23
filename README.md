# 🛡 mini-waf

> PHP(Codeigniter4) + PostgreSQL 기반 미니 웹 방화벽(WAF) 구현체  
> SQL Injection, XSS, Path Traversal 탐지 및 차단 + 관리자 대시보드

## 기술 스택

- PHP 8.2 + Codeigniter 4
- PostgreSQL 15
- Nginx
- Docker / Docker Compose
- Railway (배포)

## 탐지 공격 유형

| 유형           | 예시 패턴                               |
| -------------- | --------------------------------------- |
| SQL Injection  | `' OR 1=1--`, `UNION SELECT`            |
| XSS            | `<script>alert(1)</script>`, `onerror=` |
| Path Traversal | `../../etc/passwd`                      |

## 로컬 실행

```bash
git clone https://github.com/본인아이디/mini-waf
cd mini-waf
docker-compose up --build
# → http://localhost:8080        테스트 페이지
# → http://localhost:8080/admin  관리자 대시보드
```

## Railway 배포

1. railway.app 가입 후 GitHub 연동
2. New Project → Deploy from GitHub repo
3. 환경변수 설정: DB_HOST, DB_NAME, DB_USER, DB_PASS
4. PostgreSQL 플러그인 추가 (무료)
5. 자동 배포 완료
