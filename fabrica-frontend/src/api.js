const BASE_URL = "http://localhost/Proyecto/Api"; 
// Si en Linux lo sirves distinto (apache/nginx), lo cambiamos luego.

export async function login(payload) {
  const res = await fetch(`${BASE_URL}/auth`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  });
  return await res.json();
}

export async function getPiezas() {
  const res = await fetch(`${BASE_URL}/piezas`);
  return await res.json();
}