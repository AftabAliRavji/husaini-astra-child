document.addEventListener("DOMContentLoaded", function () {

    // Only run on screens <= 768px
    if (window.innerWidth > 768) return;

    // Select containers
    const wrapper = document.querySelector(".hic-home-wrapper .e-con-inner");
    const hero = document.querySelector(".hic-hero");
    const whatsOn = document.querySelector(".hic-whats-on");
    const featured = document.querySelector(".hic-featured");
    const latest = document.querySelector(".hic-latest-news");

    if (!wrapper || !hero || !whatsOn || !featured || !latest) return;

    // Reorder by re-appending in desired order
    wrapper.appendChild(hero);
    wrapper.appendChild(whatsOn);
    wrapper.appendChild(featured);
    wrapper.appendChild(latest);
});
