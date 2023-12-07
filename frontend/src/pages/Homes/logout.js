import { useEffect } from 'react';
import { useSession } from 'base/app';
import { api } from 'api';

/**
 * ログアウト画面
 */
export default function HomeLogout() {

    // セッション情報
    const session = useSession();

    // ログアウト処理
    useEffect(() => {
        api.logout().finally(() => {
            // 識別子クリア (ログアウト)
            session.setIdentity(null);
            session.setPermission(null);
        })
    });

    return null;
};
