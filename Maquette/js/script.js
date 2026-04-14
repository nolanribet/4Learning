document.addEventListener("DOMContentLoaded", () => {
    const avatarBtn = document.querySelector(".avatar-btn");
    const dropdown = document.querySelector(".dropdown");

    if (avatarBtn && dropdown) {
        avatarBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            dropdown.classList.toggle("active");
        });

        document.addEventListener("click", (e) => {
            if (!dropdown.contains(e.target) && !avatarBtn.contains(e.target)) {
                dropdown.classList.remove("active");
            }
        });
    }

    const gaugeFill = document.querySelector(".gauge-fill");
    if (gaugeFill) {
        const targetWidth = gaugeFill.style.width;
        gaugeFill.style.width = "0%";
        setTimeout(() => {
            gaugeFill.style.transition = "width 1s cubic-bezier(0.25, 0.8, 0.25, 1)";
            gaugeFill.style.width = targetWidth;
        }, 300);
    }
});