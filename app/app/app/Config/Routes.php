<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// 공격 시뮬레이션 처리 — POST 요청만 받음
$routes->post('/test-attack', 'Home::testAttack');