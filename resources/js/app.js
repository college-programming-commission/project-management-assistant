import './bootstrap'
import './push-notifications'

// Імпорт EasyMDE
import EasyMDE from 'easymde'
import 'easymde/dist/easymde.min.css'

// Робимо EasyMDE доступним глобально
window.EasyMDE = EasyMDE

// Функція для застосування кольорової теми
function applyColorTheme(theme) {
    // Видаляємо всі класи тем
    document.documentElement.classList.remove(
        'theme-yellow-green',
        'theme-blue-pink',
        'theme-green-purple',
        'theme-pastel',
        'theme-vibrant',
        'theme-ocean',
        'theme-sunset',
        'theme-neon',
    )

    // Додаємо новий клас теми
    if (theme) {
        document.documentElement.classList.add(`theme-${theme}`)
    } else {
        // Якщо тема не визначена, застосовуємо тему за замовчуванням
        document.documentElement.classList.add('theme-ocean')
    }

    // Додатково додаємо атрибут data-theme для кращої сумісності
    document.documentElement.setAttribute('data-theme', theme || 'ocean')

    // Застосовуємо зміни негайно
    document.body.style.transition = 'background-color 0.3s ease, color 0.3s ease'

    // Додаємо повідомлення про зміну теми
    console.log(`Застосовано кольорову тему: ${theme || 'ocean'}`)
}

// Функція для оновлення активної кнопки
function updateActiveColorButton(theme) {
    // Спочатку перевіряємо, чи елементи вже завантажені
    setTimeout(() => {
        // Видаляємо клас active з усіх кнопок
        document.querySelectorAll('.color-theme-btn').forEach((btn) => {
            btn.classList.remove('active')
        })

        // Додаємо клас active до вибраної кнопки
        const activeBtn = document.querySelector(`.color-theme-btn[data-theme="${theme || 'ocean'}"]`)
        if (activeBtn) {
            activeBtn.classList.add('active')
        }
    }, 100) // Затримка для забезпечення наявності елементів
}

// Функція для синхронізації Alpine.js компонентів з поточним станом
function syncAlpineComponents() {
    // Синхронізуємо кольорову тему з Alpine.js компонентами
    const currentTheme = localStorage.getItem('colorTheme') || 'ocean'
    if (window.Alpine) {
        document.querySelectorAll('[x-data*="activeTheme"]').forEach((el) => {
            const component = Alpine.$data(el)
            if (component && component.activeTheme !== undefined) {
                component.activeTheme = currentTheme
            }
        })
    }
}

// Функція для зміни кольорової теми
window.setColorTheme = function (theme) {
    applyColorTheme(theme)
    localStorage.setItem('colorTheme', theme)
    updateActiveColorButton(theme)

    // Оновлюємо Alpine.js стан, якщо він доступний
    if (window.Alpine) {
        document.querySelectorAll('[x-data*="activeTheme"]').forEach((el) => {
            const component = Alpine.$data(el)
            if (component && component.activeTheme !== undefined) {
                component.activeTheme = theme
            }
        })
    }
}

// Функція для перемикання теми (світла/темна)
window.toggleDarkMode = function () {
    if (document.documentElement.classList.contains('dark')) {
        document.documentElement.classList.remove('dark')
        localStorage.setItem('theme', 'light')
    } else {
        document.documentElement.classList.add('dark')
        localStorage.setItem('theme', 'dark')
    }

    // Оновлюємо Alpine.js стан для темного режиму, якщо він доступний
    if (window.Alpine) {
        document.querySelectorAll('[x-data*="darkMode"]').forEach((el) => {
            const component = Alpine.$data(el)
            if (component && component.darkMode !== undefined) {
                component.darkMode = document.documentElement.classList.contains('dark')
            }
        })
    }
}

// Функціонал перемикання теми (світла/темна та кольорова)
document.addEventListener('DOMContentLoaded', function () {
    // Перевіряємо збережену тему або використовуємо системні налаштування
    if (
        localStorage.getItem('theme') === 'dark' ||
        (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)
    ) {
        document.documentElement.classList.add('dark')
    } else {
        document.documentElement.classList.remove('dark')
    }

    // Перевіряємо збережену кольорову тему
    const savedColorTheme = localStorage.getItem('colorTheme')
    if (savedColorTheme) {
        applyColorTheme(savedColorTheme)
        updateActiveColorButton(savedColorTheme)
    } else {
        // Якщо немає збереженої теми, застосовуємо тему замовчуванням
        applyColorTheme('ocean')
        updateActiveColorButton('ocean')
    }

    // Синхронізуємо Alpine.js компоненти після завантаження DOM
    setTimeout(syncAlpineComponents, 300) // Додаємо невелику затримку для забезпечення завершення ініціалізації Alpine.js
})
