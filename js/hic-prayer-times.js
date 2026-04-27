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

        function timeToDate(timeStr) {
            if (!timeStr) return null;
            const parts = timeStr.split(':');
            if (parts.length < 2) return null;
            const h = parseInt(parts[0], 10);
            const m = parseInt(parts[1], 10);
            if (isNaN(h) || isNaN(m)) return null;
            return new Date(todayY, todayM, todayD, h, m, 0);
        }

        let nextPrayerKey = null;
        for (let i = 0; i < prayers.length; i++) {
            const tStr = bar.getAttribute('data-' + prayers[i].key);
            const tDate = timeToDate(tStr);
            if (!tDate) continue;
            if (tDate.getTime() > now.getTime()) {
                nextPrayerKey = prayers[i].key;
                break;
            }
        }

        if (!nextPrayerKey) return;

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
