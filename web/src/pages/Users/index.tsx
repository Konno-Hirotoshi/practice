import { useEffect, useState } from "react";
import { useLocation, useParams } from "react-router-dom";
import AppLayout from 'layout/AppLayout'

export default function UserIndex() {
    let { id } = useParams();
    const location = useLocation();

    const [data, setData] = useState(() => {
        return JSON.parse(sessionStorage[location.key + '_data'] ?? null) ??
            [
                { select: true, title: "test" },
                { select: false, title: "test2" },
                { select: false, title: "test3" },
            ]
    });

    useEffect(() => {

        return () => {
            setData(val => {
                sessionStorage[location.key + '_data'] = JSON.stringify(val);
                return val;
            })
        }
    }, [data, location])

    /**
     * 行追加
     */
    const addRow = () => {
        data.push({
            select: false,
            title: "test" + (data.length + 1)
        });
        setData([...data])
    }

    /**
     * 行編集
     * @param {Event} ev 
     */
    const editRow = (ev) => {
        const value = ev.target.value;
        const idx = ev.target.closest('[data-id]').dataset.id;

        data[idx].title = value;
        setData([...data])
    }

    /**
     * 行削除
     * @param {Event} ev 
     */
    const deleteRow = (ev) => {
        const idx = ev.target.closest('[data-id]').dataset.id;

        data.splice(idx, 1);
        setData([...data])
    }

    /**
     * 全選択／全選択解除
     * @param {Event} ev 
     */
    const switchAllSelect = (ev) => {
        const checked = ev.target.checked;
        data.map((row) => {
            row.select = checked;
            return row;
        })
        setData([...data]);
    }

    /**
     * 選択／選択解除
     * @param {Event} ev 
     */
    const switchSelect = (ev) => {
        const idx = ev.target.closest('[data-id]').dataset.id;

        data[idx].select = !data[idx].select;
        setData([...data])
    }

    return (
        <AppLayout>
            <h2>⚙️ 設定 {id && "#"}{id}</h2>

            <ul>
                {data.map((row, i) => <Item key={i} row={row} i={i} editRow={editRow} deleteRow={deleteRow} />)}
            </ul>

            <button onClick={addRow}>+</button>

            <hr />

            <table>
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" checked={
                                data.length !== 0 && data.length === data.filter((row) => row.select === true).length
                            } onChange={switchAllSelect} />
                        </th>
                        <th>Title</th>
                    </tr>
                </thead>
                <tbody>
                    {data.map((row, i) => (<Item2 key={i} row={row} i={i} changeSelected={switchSelect}></Item2>))}
                </tbody>
            </table>

            <hr />
        </AppLayout>
    )
};

const Item2 = (props) => {
    const { row, i, changeSelected } = props;

    return (
        <tr data-id={i}>
            <td>
                <input type="checkbox" checked={row.select} onChange={changeSelected} />
            </td>
            <td>
                {row.title}
            </td>
        </tr>
    )
}
const Item = (props) => {
    const { row, i, editRow, deleteRow } = props;

    return (
        <li data-id={i}>
            <input value={row.title} onChange={editRow} />
            <button onClick={deleteRow}>x</button>
        </li>
    )
}