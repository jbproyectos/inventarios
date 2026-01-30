const mail = document.getElementById('mail')
const password = document.getElementById('password')
const button = document.getElementById('button')

button.addEventListener('click', (e) =>{
    e.preventDefault()
    const data = {
        mail: mail.value,
        password: password.value
    }

    console.log(data)
})


