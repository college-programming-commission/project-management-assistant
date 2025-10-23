import './bootstrap'
import './push-notifications'

// Імпорт EasyMDE
import EasyMDE from 'easymde'
import 'easymde/dist/easymde.min.css'

// Робимо EasyMDE доступним глобально
window.EasyMDE = EasyMDE

// Спрощена функція для зміни кольорової теми - повністю незалежна
window.setColorTheme = function (theme) {
    // Видаляємо всі можливі теми
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

    // Додаємо обрану тему
    document.documentElement.classList.add(`theme-${theme}`)

    // Зберігаємо тему в localStorage
    localStorage.setItem('colorTheme', theme)

    // Оновлюємо активні кнопки негайно
    document.querySelectorAll('.color-theme-btn').forEach((btn) => {
        btn.classList.remove('active')
        if (btn.dataset.theme === theme) {
            btn.classList.add('active')
        }
    })

    // Якщо існує Alpine.js компонент, то оновлюємо його стан (для сумісності)
    if (window.Alpine) {
        setTimeout(() => {
            try {
                // Спробуємо оновити Alpine.js стан, якщо він існує
                document.querySelectorAll('[x-data*="activeTheme"]').forEach((el) => {
                    const data = Alpine.$data(el)
                    if (data && data.activeTheme !== undefined) {
                        data.activeTheme = theme
                    }
                })
            } catch (e) {
                // Якщо Alpine ще не ініціалізувався або не існує, просто ігноруємо
            }
        }, 0)
    }
}

// Спрощена функція для перемикання темного/світлого режиму
window.toggleDarkMode = function () {
    if (document.documentElement.classList.contains('dark')) {
        document.documentElement.classList.remove('dark')
        localStorage.setItem('theme', 'light')
    } else {
        document.documentElement.classList.add('dark')
        localStorage.setItem('theme', 'dark')
    }

    // Якщо існує Alpine.js компонент для темного режиму, то оновлюємо його стан (для сумісності)
    if (window.Alpine) {
        setTimeout(() => {
            try {
                document.querySelectorAll('[x-data*="darkMode"]').forEach((el) => {
                    const data = Alpine.$data(el)
                    if (data && data.darkMode !== undefined) {
                        data.darkMode = document.documentElement.classList.contains('dark')
                    }
                })
            } catch (e) {
                // Якщо Alpine ще не ініціалізувався або не існує, просто ігноруємо
            }
        }, 0)
    }
}

// Ініціалізація тем після завантаження DOM
document.addEventListener('DOMContentLoaded', function () {
    // Застосовуємо збережений темний/світлий режим
    const savedTheme = localStorage.getItem('theme')
    if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark')
    } else {
        document.documentElement.classList.remove('dark')
    }

    // Застосовуємо збережену кольорову тему 2
    const savedColorTheme = localStorage.getItem('colorTheme') || 'ocean'
    window.setColorTheme(savedColorTheme)
})
