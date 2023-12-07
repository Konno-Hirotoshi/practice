import { ModalDialog } from 'components/ModalDialog';
import AppLayout from 'layout/AppLayout'
import { useState } from 'react'

/**
 * ホーム画面
 */
export default function HomeIndex() {

    const [initModalOpen, setInitModalOpen] = useState(false);
    const [confirmLogoutOpen, setConfirmLogoutOpen] = useState(false);

    const onClickInit = () => {
        setInitModalOpen(true);
    }
    
    const onInitClose = () => {
        setInitModalOpen(false);
    }

    const onClickLogout = () => {
        setConfirmLogoutOpen(true);
    }

    const onLogoutYes = () => {
        setConfirmLogoutOpen(false);
    }
    
    const onLogoutNo = () => {
        setConfirmLogoutOpen(false);
    }

    return (
        <AppLayout>
            <p>Edit <code>src/App.js</code> and save to reload.</p>
            <a className="App-link" href="https://reactjs.org">Learn React</a>
            <hr />
            <button type="button" onClick={onClickInit}>初期設定</button><br/><br/>
            <button type="button" onClick={onClickLogout}>ログアウト</button>
            <ModalDialog open={initModalOpen}>
                <p>
                    初期設定が完了しました。
                </p>
                <div className="text-center">
                    <button className="btn btn-sm btn-primary" onClick={onInitClose}>OK</button>
                </div>
            </ModalDialog>
            <ModalDialog open={confirmLogoutOpen}>
                <p>
                    ログアウトしますか？
                </p>
                <div className="text-center">
                    <button className="btn btn-sm btn-primary" onClick={onLogoutYes}>はい</button> | 
                    <button className="btn btn-sm btn-primary" onClick={onLogoutNo}>いいえ</button>
                </div>
            </ModalDialog>
        </AppLayout>
    )
};