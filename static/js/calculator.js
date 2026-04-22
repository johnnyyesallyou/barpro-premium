// ===== CALCULATOR =====
document.addEventListener('DOMContentLoaded', function() {
    const guestsSlider = document.getElementById('guests');
    const hoursSlider = document.getElementById('hours');
    const cocktailsSlider = document.getElementById('cocktailsPerPerson');

    const guestsValue = document.getElementById('guestsValue');
    const hoursValue = document.getElementById('hoursValue');
    const cocktailsPerPersonValue = document.getElementById('cocktailsPerPersonValue');
    const totalPrice = document.getElementById('totalPrice');
    const totalSumInput = document.getElementById('totalSum');
    const calcDataInput = document.getElementById('calcData');

    if (!guestsSlider || !hoursSlider || !cocktailsSlider) return;

    function updateCalculation() {
        const guests = parseInt(guestsSlider.value);
        const hours = parseInt(hoursSlider.value);
        const cocktailsPerPerson = parseInt(cocktailsSlider.value);

        // Обновляем отображение значений
        guestsValue.textContent = guests;
        hoursValue.textContent = hours;
        cocktailsPerPersonValue.textContent = cocktailsPerPerson;

        // Собираем доп. услуги
        const addonCheckboxes = document.querySelectorAll('#addonServices input[type="checkbox"]');
        const addonServices = [];
        let addonsTotal = 0;

        addonCheckboxes.forEach(cb => {
            if (cb.checked) {
                addonServices.push(parseInt(cb.value));
                addonsTotal += parseInt(cb.dataset.price);
            }
        });

        // Отправляем запрос на сервер
        fetch('/calculator/api/', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRFToken': getCSRFToken(),
            },
            body: JSON.stringify({
                guests: guests,
                hours: hours,
                cocktails_per_person: cocktailsPerPerson,
                addon_services: addonServices,
            }),
        })
        .then(res => res.json())
        .then(data => {
            const total = data.total || 0;
            totalPrice.textContent = total.toLocaleString('ru-RU') + ' ₽';
            totalSumInput.value = total;
            calcDataInput.value = JSON.stringify(data);
        })
        .catch(err => {
            console.error('Ошибка расчёта:', err);
            totalPrice.textContent = 'Ошибка';
        });
    }

    // Слушатели событий
    guestsSlider.addEventListener('input', updateCalculation);
    hoursSlider.addEventListener('input', updateCalculation);
    cocktailsSlider.addEventListener('input', updateCalculation);

    document.querySelectorAll('#addonServices input[type="checkbox"]').forEach(cb => {
        cb.addEventListener('change', updateCalculation);
    });

    // Первичный расчёт
    updateCalculation();
});

// ===== LEAD FORM =====
document.addEventListener('DOMContentLoaded', function() {
    const leadForm = document.getElementById('leadForm');
    if (!leadForm) return;

    leadForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submitBtn');
        const formMessage = document.getElementById('formMessage');

        submitBtn.disabled = true;
        submitBtn.textContent = 'Отправка...';

        const formData = new FormData(leadForm);
        const data = Object.fromEntries(formData.entries());

        fetch('/api/lead/', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRFToken': getCSRFToken(),
            },
            body: JSON.stringify(data),
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                formMessage.textContent = 'Заявка отправлена! Мы свяжемся с вами в ближайшее время.';
                formMessage.className = 'lead-form__message lead-form__message--success';
                leadForm.reset();
            } else {
                formMessage.textContent = result.error || 'Произошла ошибка. Попробуйте ещё раз.';
                formMessage.className = 'lead-form__message lead-form__message--error';
            }
        })
        .catch(err => {
            formMessage.textContent = 'Ошибка соединения. Попробуйте ещё раз.';
            formMessage.className = 'lead-form__message lead-form__message--error';
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Отправить заявку';
        });
    });
});

// ===== CSRF TOKEN =====
function getCSRFToken() {
    const cookieValue = document.cookie
        .split('; ')
        .find(row => row.startsWith('csrftoken='))
        ?.split('=')[1];
    return cookieValue || '';
}
