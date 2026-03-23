<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>Mini WAF - 테스트</title>
  <style>
    body { font-family: sans-serif; max-width: 700px; margin: 60px auto; padding: 0 20px; }
    input { width: 100%; padding: 10px; font-size: 14px; border: 1px solid #ccc; border-radius: 6px; margin: 8px 0; }
    button { padding: 10px 24px; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; }
    button:hover { background: #1d4ed8; }
    .badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
    .safe { background: #dcfce7; color: #166534; }   /* 정상 요청 시 초록 배지 */
    .warn { background: #fef9c3; color: #854d0e; }   /* 공격 페이로드 버튼 노란 배지 */
    pre { background: #f3f4f6; padding: 16px; border-radius: 8px; font-size: 13px; overflow-x: auto; }
    h2 { margin-top: 40px; font-size: 16px; color: #374151; }
  </style>
</head>
<body>

<h1>🛡 Mini WAF — 공격 탐지 테스트</h1>
<p>아래 입력창에 공격 페이로드를 입력해보세요.</p>

<?php /* 이전 요청이 정상이었을 때 안전 메시지 표시 */ ?>
<?php if (!empty($message)): ?>
  <p class="badge safe"><?= $message ?></p>
<?php endif; ?>

<?php /* 공격 테스트 폼 — POST로 /test-attack에 전송 */ ?>
<form method="POST" action="/test-attack">
  <label>검색어 / 입력값</label>
  <input type="text" name="query"
    placeholder="예: ' OR 1=1-- / <script>alert(1)</script> / ../../etc/passwd">
  <br><br>
  <button type="submit">공격 시뮬레이션</button>
</form>

<h2>⚡ 빠른 테스트 페이로드</h2>
<?php /* 클릭하면 입력창에 페이로드 자동 입력됨 */ ?>
<div style="display:flex;gap:8px;flex-wrap:wrap">
  <?php foreach ([
    "' OR 1=1--",                        // SQL Injection
    "UNION SELECT * FROM users",          // SQL Injection
    "<script>alert('xss')</script>",      // XSS
    "../../etc/passwd",                   // Path Traversal
    "DROP TABLE users;",                  // SQL Injection
  ] as $p): ?>
    <span class="badge warn" style="cursor:pointer"
      onclick="document.querySelector('[name=query]').value='<?= htmlspecialchars($p) ?>'"
    ><?= htmlspecialchars($p) ?></span>
  <?php endforeach; ?>
</div>

</body>
</html>