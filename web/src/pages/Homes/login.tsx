import { useState } from 'react';
import { useSession } from '@/base/app';
import { api } from 'api';

/**
 * ログイン画面
 */
export default function HomeLogin() {

    // ID
    const [id, setId] = useState('');

    // パスワード
    const [password, setPassword] = useState('');

    // ログイン処理フラグ
    const [inProgress, setInProgress] = useState(false);

    // ログイン処理結果メッセージ
    const [message, setMessage] = useState(null);

    // セッション情報
    const session = useSession();

    // ログイン処理
    const onLogin = () => {
        setInProgress(true);
        api.login(id, password).then((response: any) => {
            // セッション保存
            session.setIdentity(response.identity);
            session.setPermission(response.permission);
        }).catch((response) => {
            if (response.errors._alert) {
                setMessage(response.errors._alert);
            } else {
                onBack();
            }
        });
    }

    // 戻る処理
    const onBack = () => {
        setInProgress(false);
        setMessage(null);
    }

    return (
        <div className="mx-auto mb-5 text-center justify-content-center align-items-center d-flex">
            {!inProgress ? (
                <Form onSubmit={onLogin}>
                    <IdInput id={id} setId={setId} />
                    <PasswordInput password={password} setPassword={setPassword} />
                    <SubmitButton>ログイン</SubmitButton>
                </Form>
            ) : (
                <>
                    {message ? (
                        <div>
                            <p>{message}</p>
                            <BackButton onClick={onBack}>戻る</BackButton>
                        </div>
                    ) : (
                        <span className="spinner-border text-primary" role="status" />
                    )}
                </>
            )}

        </div>
    );
};

const Form = (props) => {
    const onSubmit = (event) => {
        event.preventDefault();
        props.onSubmit(event);
    }
    return <form {...props} onSubmit={onSubmit} />
}

const IdInput = ({ id, setId }) => {
    return (
        <input
            type="text"
            placeholder="ID"
            autoComplete="username"
            value={id}
            onChange={(event) => setId(event.target.value)}
            className="my-1 d-block"
        />
    );
}

const PasswordInput = ({ password, setPassword }) => {
    return (
        <input
            type="password"
            placeholder="パスワード"
            autoComplete="current-password"
            value={password}
            onChange={(event) => setPassword(event.target.value)}
            className="my-1 d-block"
        />
    );
}

const SubmitButton = (props) => {
    return <button type="submit" className="btn btn-success my-2" {...props} />
}

const BackButton = (props) => {
    return <button type="button" className="btn btn-success my-2" {...props} />
}
