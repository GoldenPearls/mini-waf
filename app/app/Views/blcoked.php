<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>🚫 차단됨</title>
  <style>
    body { font-family: sans-serif; max-width: 600px; margin: 100px auto; text-align: center; padding: 0 20px; }
    .box { background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 40px; }
    h1 { color: #dc2626; font-size: 28px; }
    .type { display: inline-block; background: #dc2626; color: white; padding: 4px 14px; border-radius: 12px; font-size: 13px; }
    pre { background: #f3f4f6; text-align: left; padding: 16px; border-radius: 8px; font-size: 13px; word-break: break-all; }
    a { color: #2563eb; text-decoration: none; }
  </style>
</head>
<body>
  <div class="box">
    <h1>🚫 요청이 차단되었습니다</h1>
    <p>악성 패턴이 탐지되어 접근이 차단되었습니다.</p>

    <?php /* 탐지된 공격 유형 표시 (SQL_INJECTION / XSS / PATH_TRAVERSAL) */ ?>
    <p><span class="type"><?= htmlspecialchars($attackType ?? 'UNKNOWN') ?></span></p>

    <?php /* 실제 탐지된 페이로드 표시 — htmlspecialchars로 2차 XSS 방지 */ ?>
    <p><strong>탐지된 페이로드:</strong></p>
    <pre><?= htmlspecialchars($payload ?? '') ?></pre>

    <p>
      <a href="/">← 돌아가기</a> |
      <a href="/admin">📊 로그 보기</a>
    </p>
  </div>
</body>
</html>