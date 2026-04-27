document.addEventListener('DOMContentLoaded', function () {
    const bars = document.querySelectorAll('.hic-prayer-times-bar');
    if (!bars.length) return;

    bars.forEach(bar => {
        const prayers = [
            { key: 'imsaak',  label: 'Imsaak'  },
            { key: 'fajr',    label: 'Fajr'    },
            { key: 'sunrise', label: 'Sunrise' },
            { key: 'zohr',    label: 'Zohr'    },
            { key: 'sunset',  label: 'Sunset'  },
            { key: 'maghrib', label: 'Maghrib' }
        ];

        const now = new Date();
        const todayY = now.getFullYear();
        const todayM = now.getMonth();
        const todayD = now.getDate();

        function timeToDate(timeStr, offsetDays = 0) {
            if (!timeStr) return null;
            const [h, m] = timeStr.split(':').map(Number);
            return new Date(todayY, todayM, todayD + offsetDays, h, m, 0);
        }

        // 1. Try to find next prayer today
        let nextPrayerKey = null;
        for (let i = 0; i < prayers.length; i++) {
            const tStr = bar.getAttribute('data-' + prayers[i].key);
            const tDate = timeToDate(tStr);
            if (tDate && tDate > now) {
                nextPrayerKey = prayers[i].key;
                break;
            }
        }

        // 2. If none found → tomorrow Fajr
        if (!nextPrayerKey) {
            nextPrayerKey = 'tomorrow-fajr';
        }

        // 3. Highlight the correct item
        const items = bar.querySelectorAll('.hic-pt-item');
        items.forEach(item => {
            const key = item.getAttribute('data-prayer');
            if (key === nextPrayerKey) {
                item.classList.add('hic-pt-next');
            } else {
                item.classList.remove('hic-pt-next');
            }
        });
    });
});

window.addEventListener('load', function () {
    const g = document.querySelector('.hic-pt-gregorian');
    const h = document.querySelector('.hic-pt-islamic');

    const mg = document.querySelector('.hic-mobile-gregorian');
    const mh = document.querySelector('.hic-mobile-hijri');

    if (g && h && mg && mh) {
        mg.textContent = g.textContent;
        mh.textContent = h.textContent;
    }
});
