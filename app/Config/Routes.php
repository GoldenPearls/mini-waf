
// 메인 페이지 — 공격 테스트 폼
$routes->get('/', 'Home::index');

// 공격 시뮬레이션 처리 — POST 요청만 받음
$routes->post('/test-attack', 'Home::testAttack');