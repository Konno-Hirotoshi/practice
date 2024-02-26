import AppLayout from '@/layout/AppLayout'

export default function ReportIndex() {
    return (
        <AppLayout>
            <h2>📝 帳票出力</h2>
            <div className="px-4 py-3 my-3 bg-light">
                <h5>検索条件</h5>
                <div className="d-inline-block my-2">名称：<input className="form-control form-control-sm d-inline w-auto" /></div>
                <div className="d-inline-block my-2">種別：<input className="form-control form-control-sm d-inline w-auto" /></div>
                <div className="d-inline-block my-2">備考：<input className="form-control form-control-sm d-inline w-auto" /></div>
                <div className="d-inline-block my-2">選択備考：<input className="form-control form-control-sm d-inline w-auto" /></div>
                <div className="d-inline-block my-2">備考１：<input className="form-control form-control-sm d-inline w-auto" /></div>
                <div className="d-inline-block my-2">備考２：<input className="form-control form-control-sm d-inline w-auto" /></div>
                <div className="d-inline-block my-2">備考３：<input className="form-control form-control-sm d-inline w-auto" /></div>
                <div className="d-inline-block my-2">メモ欄：<input className="form-control form-control-sm d-inline w-auto" /></div>
                <button className="btn btn-sm btn-info mx-2" onClick={() => document.getElementById('table').classList.toggle('d-none')}>検索</button>
            </div>
            <div id="table" className="d-none">
                <table className="border w-100">
                    <thead>
                        <tr className="bg-dark text-white"><td style={{width:60}}></td><td>名称</td><td>備考</td></tr>
                    </thead>
                    <tbody>
                        <tr><td><button>選択</button></td><td>テスト ***</td><td>--</td></tr>
                        <tr><td><button>選択</button></td><td>*** ***</td><td>--</td></tr>
                        <tr><td><button>選択</button></td><td>*** ***</td><td>--</td></tr>
                        <tr><td><button>選択</button></td><td>*** ***</td><td>--</td></tr>
                        <tr><td><button>選択</button></td><td>*** ***</td><td>--</td></tr>
                        <tr><td><button>選択</button></td><td>*** ***</td><td>--</td></tr>
                        <tr><td><button>選択</button></td><td>*** ***</td><td>--</td></tr>
                        <tr><td><button>選択</button></td><td>*** ***</td><td>--</td></tr>
                        <tr><td><button>選択</button></td><td>*** ***</td><td>--</td></tr>
                        <tr><td><button>選択</button></td><td>*** ***</td><td>--</td></tr>
                        <tr><td><button>選択</button></td><td>*** ***</td><td>--</td></tr>
                        <tr><td><button>選択</button></td><td>*** ***</td><td>--</td></tr>
                        <tr><td><button>選択</button></td><td>*** ***</td><td>--</td></tr>
                        <tr><td><button>選択</button></td><td>*** ***</td><td>--</td></tr>
                        <tr><td><button>選択</button></td><td>*** ***</td><td>--</td></tr>
                        <tr><td><button>選択</button></td><td>*** ***</td><td>--</td></tr>
                        <tr><td><button>選択</button></td><td>*** ***</td><td>--</td></tr>
                        <tr><td><button>選択</button></td><td>*** ***</td><td>--</td></tr>
                        <tr><td><button>選択</button></td><td>*** ***</td><td>--</td></tr>
                        <tr><td><button>選択</button></td><td>*** ***</td><td>--</td></tr>
                        <tr><td><button>選択</button></td><td>*** ***</td><td>--</td></tr>
                        <tr><td><button>選択</button></td><td>*** ***</td><td>--</td></tr>
                    </tbody>
                </table>
                <div className="m-2 text-center">
                    Prev 1 <u>2</u> 3 4 5 Next
                </div>
            </div>
        </AppLayout>
    )
}
