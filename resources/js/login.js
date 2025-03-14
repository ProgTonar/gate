document.addEventListener('DOMContentLoaded', function () {
    const loginInput = document.getElementById('login');
    const passwordInput = document.getElementById('password');
    const submitButton = document.getElementById('submitBtn');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    submitButton.addEventListener('click', async function (event) {
        event.preventDefault();

        const login = loginInput.value.trim();
        const password = passwordInput.value.trim();

        if (!login || !password) {
            alert('Пожалуйста, заполните все поля!');
            return;
        }

        try {
            const response = await axios.post('/login', {
                login: login,
                password: password
            }, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            console.log('Ответ сервера:', response.data);
            window.location.href = response.data.url;
        } catch (error) {
            console.error('Ошибка:', error);

            if (error.response) {
                alert(error.response.data.message || 'Неверный логин или пароль.');
            } else if (error.request) {
                alert('Сервер не отвечает. Пожалуйста, попробуйте позже.');
            } else {
                alert('Ошибка отправки запроса.');
            }
        }
    });
});
