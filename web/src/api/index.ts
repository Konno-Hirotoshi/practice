export const api = {
    // ログイン
    login: (id, password) => new Promise((resolve, reject) => {
        setTimeout(() => {
            if (id.length === 0) {
                reject({
                    errors: {
                        _alert: 'ID、またはパスワードに誤りがあります。'
                    }
                });
                return;
            }
            const identity = { "name": id };
            const permission = { "users": 1, "reports": 1, "cost": 1, "sales": 1, "users.add": 1, "users.edit":1 };
            resolve({ identity, permission });
        }, 500 + Math.random() * 2000);
    }),
    // ログアウト
    logout: () => new Promise((resolve, reject) => {
        setTimeout(() => {
            resolve(null);
        }, 300 + Math.random() * 1000);
    }),

}