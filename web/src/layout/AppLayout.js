import { memo } from 'react';
import { NavLink } from 'react-router-dom';
import { useSession } from 'base/app'
import useLocalStorage from 'base/localStorage';

/**
 * ナビゲーションメニュー項目
 */
const MENU_ROWS = [
    { path: "/", label: '🏠 ホーム', isPublic: true },
    { path: "/sales", label: '💰 売上明細' },
    { path: "/cost", label: '📔 原価明細' },
    { path: "/reports", label: '📝 帳票出力' },
    { path: "/users", label: '⚙️ 設定' },
];

/**
 * ナビゲーションメニュー
 */
const NavPanel = memo(function NavPanel() {

    const { identity, permission } = useSession();
    const [sidebar, setSidebar] = useLocalStorage('sidebar', true);

    // 未認証時はナビゲーションメニューを表示しない
    if (!identity) {
        return null
    }

    // 遷移可能なメニュー項目のみ表示する
    const menuList = MENU_ROWS.filter(({ path, isPublic }) => {
        const key = path.split('/').slice(1, 3).join('.');
        return (isPublic || (permission && permission[key]));
    });

    // Note: ホームリンクだけはactiveクラスの自動付与ロジックが誤判定するため、このロジックを使用する
    const activeClass = { '/': (isActive) => isActive && window.location.pathname === '/' ? 'active' : '' }

    // Note: ログアウトはaタグ (index.htmlをリロードするため)
    return (
        <nav className={sidebar ? 'fixed' : null}>
            <span className="navicon" onClick={() => setSidebar(!sidebar)}>≡</span>
            <ul className="nav1st">
                {menuList.map(({ path, label }) => <li key={path}>
                    <NavLink to={path} className={activeClass[path]}>{label}</NavLink>
                    {path === "/users" ?
                        <ul className="navchild">
                            <li>
                                <NavLink to="/users/edit/1">⚙️ user 01</NavLink>
                                <NavLink to="/users/edit/2">⚙️ user 02</NavLink>
                                <NavLink to="/users/edit/3">⚙️ user 03</NavLink>
                                <NavLink to="/users/add">⚙️ Add</NavLink>
                            </li>
                        </ul>
                        : null}
                </li>)}
            </ul>
            <ul className="nav2nd">
                <li><NavLink to="/profile" className="navprofile">👤 {identity.name}</NavLink></li>
                <li><a href="/logout" className="navlogout">🔑 ログアウト</a></li>
            </ul>
        </nav>
    );
});

/**
 * アプリケーション共通レイアウト
 */
export default function AppLayout({ children }) {
    return (
        <>
            <NavPanel />
            <main>
                {children}
            </main>
        </>
    );
};
