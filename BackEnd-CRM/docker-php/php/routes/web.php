<?php

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\DealsController;
use App\Controllers\EmailController;
use App\Controllers\EstimatesController;
use App\Controllers\HomeController;
use App\Controllers\LeadsController;
use App\Controllers\InvoicesController;
use App\Controllers\PaymentsController;
use App\Controllers\CalendarController;
use App\Controllers\MitraController;
use App\Controllers\ClientsController;
use App\Controllers\PipelineController;
use App\Controllers\TemplateController;
use App\Controllers\SubscriptionsController;
use App\Controllers\TokenLoginController;
use App\Middleware\Authenticate;
use App\Middleware\EnsureStaff;
use App\Core\Router;

/** @var App\Core\Application $app */
$router = $app->router();

// Redirect /login to frontend login page (SSO via token)
$router->get('/login', function () {
    header('Location: http://localhost:8082/page-login.php', true, 302);
    exit;
});
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

// Token-based SSO from frontend
$router->get('/auth/token-login', [TokenLoginController::class, 'handle']);

$router->get('/', [HomeController::class, 'index']);

$router->group([
    'middleware' => [Authenticate::class],
], function (Router $router): void {
    $router->get('/dashboard', [DashboardController::class, 'admin']);
    $router->get('/dashboard/pegawai', [DashboardController::class, 'employee']);
    $router->get('/dashboard/pelanggan', [DashboardController::class, 'customer']);

    $router->group([
        'prefix' => '/dashboard',
    ], function (Router $router): void {
        $router->get('/email', [EmailController::class, 'index']);
        $router->get('/email/{id}', [EmailController::class, 'show']);
        $router->post('/email/compose', [EmailController::class, 'compose']);
        $router->post('/email/{id}/star', [EmailController::class, 'star']);
        $router->post('/email/{id}/unstar', [EmailController::class, 'unstar']);
        $router->post('/email/{id}/read', [EmailController::class, 'markRead']);
        $router->post('/email/{id}/unread', [EmailController::class, 'markUnread']);
        $router->post('/email/{id}/move', [EmailController::class, 'move']);
        $router->post('/email/bulk', [EmailController::class, 'bulk']);

        $router->get('/{template}.php', [TemplateController::class, 'show']);
        $router->post('/{template}.php', [TemplateController::class, 'show']);
        $router->post('/{template}', [TemplateController::class, 'show']);

        $router->group([
            'prefix' => '/pegawai',
        ], function (Router $router): void {
            $router->get('/{template}.php', [TemplateController::class, 'show']);
        });

        $router->group([
            'prefix' => '/pelanggan',
        ], function (Router $router): void {
            $router->get('/{template}.php', [TemplateController::class, 'show']);
        });
    });
});

$router->group([
    'prefix' => '/news',
], function (Router $router): void {
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/{slug}', [HomeController::class, 'show']);
});

$router->group([
    'prefix' => '/crm',
    'middleware' => [Authenticate::class, EnsureStaff::class],
], function (Router $router): void {
    $router->get('/mitra', [MitraController::class, 'grid']);
    $router->get('/mitra/table', [MitraController::class, 'table']);
    $router->get('/mitra/create', [MitraController::class, 'create']);
    $router->post('/mitra', [MitraController::class, 'store']);
    $router->get('/mitra/{id}/edit', [MitraController::class, 'edit']);
    $router->put('/mitra/{id}', [MitraController::class, 'update']);
    $router->delete('/mitra/{id}', [MitraController::class, 'destroy']);

    $router->get('/clients', [ClientsController::class, 'index']);
    $router->get('/clients/grid', [ClientsController::class, 'grid']);
    $router->post('/clients', [ClientsController::class, 'handle']);

    $router->get('/deals', [DealsController::class, 'index']);
    $router->post('/deals', [DealsController::class, 'store']);
    $router->put('/deals/{id}', [DealsController::class, 'update']);
    $router->delete('/deals/{id}', [DealsController::class, 'destroy']);

    $router->get('/estimates', [EstimatesController::class, 'index']);
    $router->post('/estimates', [EstimatesController::class, 'store']);
    $router->put('/estimates/{id}', [EstimatesController::class, 'update']);
    $router->delete('/estimates/{id}', [EstimatesController::class, 'destroy']);

    $router->get('/invoices', [InvoicesController::class, 'index']);
    $router->post('/invoices', [InvoicesController::class, 'store']);
    $router->put('/invoices/{id}', [InvoicesController::class, 'update']);
    $router->delete('/invoices/{id}', [InvoicesController::class, 'destroy']);

    $router->get('/leads', [LeadsController::class, 'index']);
    $router->post('/leads', [LeadsController::class, 'store']);
    $router->put('/leads/{id}', [LeadsController::class, 'update']);
    $router->delete('/leads/{id}', [LeadsController::class, 'destroy']);

    $router->get('/pipeline', [PipelineController::class, 'index']);
    $router->post('/pipeline', [PipelineController::class, 'store']);
    $router->put('/pipeline/{id}', [PipelineController::class, 'update']);
    $router->delete('/pipeline/{id}', [PipelineController::class, 'destroy']);

    $router->get('/subscriptions', [SubscriptionsController::class, 'index']);
    $router->post('/subscriptions', [SubscriptionsController::class, 'store']);
    $router->delete('/subscriptions/{id}', [SubscriptionsController::class, 'destroy']);
});

$router->group([
    'prefix' => '/crm',
    'middleware' => [Authenticate::class],
], function (Router $router): void {
    $router->get('/payments', [PaymentsController::class, 'index']);
    $router->post('/payments', [PaymentsController::class, 'store']);
    $router->post('/payments/{id}/verify', [PaymentsController::class, 'verify']);
    $router->put('/payments/{id}', [PaymentsController::class, 'update']);
    $router->delete('/payments/{id}', [PaymentsController::class, 'destroy']);
});

$router->group([
    'prefix' => '/api/calendar',
    'middleware' => [Authenticate::class],
], function (Router $router): void {
    $router->get('/events', [CalendarController::class, 'index']);
    $router->post('/events', [CalendarController::class, 'store']);
    $router->patch('/events/{id}', [CalendarController::class, 'update']);
    $router->delete('/events/{id}', [CalendarController::class, 'destroy']);
});

$router->group([
    'middleware' => [Authenticate::class],
], function (Router $router): void {
    $router->get('/clients-grid', [ClientsController::class, 'grid']);
    $router->get('/deals-grid', [DealsController::class, 'index']);
    $router->get('/estimates', [EstimatesController::class, 'index']);
    $router->get('/invoices', [InvoicesController::class, 'index']);
    $router->get('/payments', [PaymentsController::class, 'index']);
    $router->get('/subscriptions', [SubscriptionsController::class, 'index']);
    $router->get('/leads-grid', [LeadsController::class, 'index']);
    $router->get('/pipeline', [PipelineController::class, 'index']);

    $router->get('/deals-grid.php', [DealsController::class, 'index']);
    $router->get('/estimates.php', [EstimatesController::class, 'index']);
    $router->get('/invoices.php', [InvoicesController::class, 'index']);
    $router->get('/payments.php', [PaymentsController::class, 'index']);
    $router->get('/subscriptions.php', [SubscriptionsController::class, 'index']);
    $router->get('/leads-grid.php', [LeadsController::class, 'index']);
    $router->get('/pipeline.php', [PipelineController::class, 'index']);
});

$router->get('/{template}.php', [TemplateController::class, 'show']);
$router->post('/{template}.php', [TemplateController::class, 'show']);
$router->get('/{template}', [TemplateController::class, 'show']);
$router->post('/{template}', [TemplateController::class, 'show']);
