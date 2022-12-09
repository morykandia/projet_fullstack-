import React, {useState, useEffect} from 'react';
import Login from "./Login"


function App() {
  const [jwt, setJwt] = useState<string>("")

  useEffect(() => {
    if (jwt === "") {
      return
    }

    fetch("http://localhost:1221", {
            method: "GET",
            mode: "cors",
            credentials: "include",
            headers: new Headers({
                "Authorization": "Bearer " + jwt
            })
        })
            .then(data => data.json())
            .then(json => console.log(json))
  }, [jwt])

  return (
    <div className="App">
     <h1>Bonjour react = {jwt}</h1>
     <Login setJwt={setJwt}/>
    </div>
  );
}

export default App;
