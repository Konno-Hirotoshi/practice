import { createRoot } from 'react-dom/client';
import { Navigate } from 'react-router-dom';

import MyApp from './base/app'
import HomeIndex from './pages/Homes/index';
import HomeLogin from './pages/Homes/login';
import HomeLogout from './pages/Homes/logout';
import UserIndex from './pages/Users';
import ReportIndex from './pages/Reports/index';
import PageNotFound from './layout/PageNotFound';
import PageForbidden from './layout/PageForbidden';

// ================================================================================
//  * ルーティング（認証時）
// ================================================================================
const authorized = [
    { path: "/", element: <HomeIndex />, isPublic: true },
    { path: "/sales", element: <PageForbidden /> },
    { path: "/cost", element: <PageForbidden /> },
    { path: "/users", element: <UserIndex /> },
    { path: "/users/add", element: <UserIndex /> },
    { path: "/users/edit/:id", element: <UserIndex /> },
    { path: "/reports", element: <ReportIndex /> },
    { path: "/login", element: <Navigate to="/" replace />, isPublic: true },
    { path: "/logout", element: <HomeLogout />, isPublic: true },
    { path: "*", element: <PageNotFound />, isPublic: true },
];

// ================================================================================
//  * ルーティング (未認証時)
// ================================================================================
const unauthorized = [
    { path: "/login", element: <HomeLogin />, isPublic: true },
    { path: "*", element: <Navigate to="/login" replace />, isPublic: true },
];

// ================================================================================
//  * Booting React
// ================================================================================
const app = document.getElementById('app');
const root = createRoot(app);

root.render(
    <MyApp routes={{
        authorized,
        unauthorized,
        PageForbidden
    }} />
);
