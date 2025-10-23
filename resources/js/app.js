import './bootstrap'
import './push-notifications'

// Імпорт EasyMDE
import EasyMDE from 'easymde'
import 'easymde/dist/easymde.min.css'

// Робимо EasyMDE доступним глобально
window.EasyMDE = EasyMDE

// Спрощена функція для зміни кольорової теми
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

    // Оновлюємо активні кнопки (з невеликою затримкою, щоб дочекатись DOM)
    setTimeout(() => {
        document.querySelectorAll('.color-theme-btn').forEach((btn) => {
            btn.classList.remove('active')
            if (btn.dataset.theme === theme) {
                btn.classList.add('active')
            }
        })
    }, 0)
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

    // Застосовуємо збережену кольорову тему
    const savedColorTheme = localStorage.getItem('colorTheme') || 'ocean'
    window.setColorTheme(savedColorTheme)
})
