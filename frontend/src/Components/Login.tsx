import {SetStateAction, useState, Dispatch} from 'react'

export interface ILoginProps {
    setJwt: Dispatch<SetStateAction<string>>
}

export default function Login ({setJwt}: ILoginProps) {
    const [email, setEmail] = useState<string>("")
    const [password, setPassword] = useState<string>("")

    const handleSubmit = (e: any) => {
        e.preventDefault();
        fetch("http://localhost:1221/login", {
            method: "POST",
            mode: "cors",
            credentials: "include",
            headers: new Headers({
                "Authorization": "Basic " + btoa(`${email}:${password}`)
            })
        })
            .then(data => data.json())
            .then(json => {
                if (json.jwt !== undefined) {
                    setJwt(json.jwt)
                }
            })
    }

    return (
        <form onSubmit={handleSubmit}>
            <input type="email" name="email" onChange={e => setEmail(e.target.value)} /> <br/>
            <input type="password" name="password" onChange={e => setPassword(e.target.value)}/> <br/>
            <button type="submit">SignIn</button>
        </form>
    );
}