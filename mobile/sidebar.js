class SideBar {
    constructor() {
        this.threshold = 100;
        this.transformX = 0;
        this.sidebarEl = document.getElementById('sidebar');
        this.sidebarMenu = document.getElementById('menu');
        this.slideInEl = document.getElementById('slideMenu');
        this.menuButton = document.getElementById('menuButton');
        this.closeButton = document.getElementById('closeButton');
        this.overlayEl = document.getElementById('overlay');

        this.menuIn = this.menuIn.bind(this);
        this.menuOut = this.menuOut.bind(this);
        this.touchStart = this.touchStart.bind(this);
        this.touchEnd = this.touchEnd.bind(this);
        this.touchMoveIn = this.touchMoveIn.bind(this);
        this.touchMoveOut = this.touchMoveOut.bind(this);
        this.slideInEnd = this.slideInEnd.bind(this);

        this.addListeners();
    }

    addListeners() {
        this.menuButton.addEventListener('click', this.menuIn);
        this.slideInEl.addEventListener('touchstart', this.touchStart);
        this.slideInEl.addEventListener('touchmove', this.touchMoveIn);
        this.slideInEl.addEventListener('touchend', this.slideInEnd);
        this.sidebarMenu.addEventListener('touchstart', this.touchStart);
        this.sidebarMenu.addEventListener('touchmove', this.touchMoveOut);
        this.sidebarMenu.addEventListener('touchend', this.touchEnd);
        this.overlayEl.addEventListener('click', this.menuOut);
    }

    menuIn() {
        this.sidebarEl.classList.remove('hidden');
        this.closeButton.addEventListener('click', this.menuOut);
    }

    menuOut() {
        this.menuButton.addEventListener('click', this.menuIn);
        this.sidebarEl.classList.add('hidden');
    }

    touchStart(e) {
        this.startX = e.touches[0].screenX;
    }

    touchMoveOut(e) {
        this.transformX = (e.touches[0].screenX - this.startX);
        this.transformX = this.transformX <= 0 ? this.transformX : 0;
        this.sidebarMenu.style.transform = `translate(${this.transformX}px, 0)`;
    }

    touchMoveIn(e) {
        this.transformX = (e.touches[0].screenX - this.startX) - this.sidebarMenu.clientWidth;
        this.transformX = this.transformX >= 0 ? 0 : this.transformX;
        this.sidebarMenu.style.transform = `translate(${this.transformX}px, 0)`
    }

    touchEnd() {
        this.sidebarMenu.style.transform = '';
        this.transformX < -this.threshold ? this.menuOut() : this.menuIn();
    }

    slideInEnd() {
        this.sidebarMenu.style.transform = '';
        this.transformX > (this.threshold - this.sidebarMenu.clientWidth) ? this.menuIn() : this.menuOut();
    }
}

new SideBar();