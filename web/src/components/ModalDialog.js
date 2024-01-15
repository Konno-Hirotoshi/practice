import { useEffect, useRef } from 'react'

export const ModalDialog = ({ open, children }) => {
    const ref = useRef();
    
    useEffect(() => {
        if (open && !ref.current.open) {
            ref.current.showModal();
        }
        if (!open && ref.current.open) {
            ref.current.close();
        }
    });

    return (
        <dialog ref={ref}>
            {children}
        </dialog>
    );
}