
import { createContext, useContext } from 'react';
import { BrowserRouter, Route, Routes } from 'react-router-dom';
import useLocalStorage from './localStorage'
import 'resources/common.css';

const SessionContext = createContext();

/**
 * Session context hook
 */
export const useSession = () => {
    return useContext(SessionContext);
}

/**
 * Application Main
 */
export default function MyApp({ routes: { authorized, unauthorized, PageForbidden } }) {
    const [identity, setIdentity] = useLocalStorage('identity', null);
    const [permission, setPermission] = useLocalStorage('permission', null);

    const routeElements = (identity ? authorized : unauthorized).map(({ path, element, isPublic }) => {
        const key = path.split('/').slice(1, 3).join('.');
        if (!isPublic && (!permission || !permission[key])) {
            element = <PageForbidden />;
        }
        return <Route key={key} path={path} element={element} />
    });

    const session = {
        identity,
        setIdentity,
        permission,
        setPermission,
    };

    return (
        <SessionContext.Provider value={session}>
            <BrowserRouter>
                <Routes>
                    {routeElements}
                </Routes>
            </BrowserRouter>
        </SessionContext.Provider>
    )
}