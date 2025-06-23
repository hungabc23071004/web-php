<?php
session_start();
require_once __DIR__ . '/../controllers/AdminController.php';

$controller = new AdminController();

$action = isset($_GET['action']) ? $_GET['action'] : 'index';

switch ($action) {
    case 'index':
        $controller->index();
        break;
    case 'create':
        $controller->create();
        break;
    case 'edit':
        $controller->edit();
        break;
    case 'update':
        $controller->update();
        break;
    case 'delete':
        $controller->delete();
        break;
    case 'reports':
        $controller->reports();
        break;
    case 'profile':
        $controller->profile();
        break;
    case 'updateProfile':
        $controller->updateProfile();
        break;
    case 'categories':
        $controller->categories();
        break;
    case 'addCategory':
        $controller->addCategory();
        break;
    case 'editCategory':
        $controller->editCategory();
        break;
    case 'updateCategory':
        $controller->updateCategory();
        break;
    case 'deleteCategory':
        $controller->deleteCategory();
        break;
    case 'addSale':
        $controller->addSale();
        break;
    case 'logout':
        $controller->logout();
        break;
    default:
        $controller->index();
        break;
}
?> 