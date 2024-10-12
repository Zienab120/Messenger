import { useEffect } from "react";

const { useState } = require("react");

const NewMessageInput = ({value, onchange, onSend}) => {
    const input = useState();

    const onInputKeyDown = (ev) => {
        if(ev.key === "Enter" && !ev.shiftKey){
            ev.preventDefault();
            onSend();
        }
    };

    const onChangeEvent = (ev) => {
        setTimeout(()=> {
            adjustHeight();
        },10);
        onChangeEvent(ev);
    };

    const adjustHeight = () => {
        setTimeout(() => {
            input.current.style.height = "auto";
            input.current.style.height = input.current.scrollHeight + 1 + "px";
        }, 100);
    };
    useEffect(() => {
        adjustHeight();
    },[value]);

    return (
        <textarea
            ref={input}
            value={value}
            rows="1"
            placeholder="Type a message"
            onKeyDown={onInputKeyDown}
            onChange={(ev) => onChangeEvent(ev)}
            className="input input-boarded w-full rounded-r-none resize-none overflow-y-auto max-h-40"
            >

            </textarea>
    );
};

export default NewMessageInput;