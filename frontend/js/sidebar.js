import { views } from './views.js';

export const sidebarHandlers = {
  init(mapController, authController) {
    this.mapController = mapController;
    this.authController = authController;
    this.setupEventDelegation();
  },

  setupEventDelegation() {
    const sidebar = document.getElementById('sidebar-content');
    if (!sidebar) return;

    sidebar.addEventListener('click', (e) => {
      if (e.target.id === 'close-sidebar-btn') {
        this.mapController.flyTo([53.757643, 28.051186], 7)
        this.handleClose();
        this.mapController.flyTo({
          center: [28.051186, 53.757643], // [lng, lat] порядок для MapLibre
          zoom: 7
        });
      }
      if (e.target.classList.contains('sidebar-button')) {
        this.handleVisit(e);
      }
      if (e.target.classList.contains('note-button')) {
        this.handleNote(e);
      }
      if (e.target.classList.contains('logout-button')) {
        this.handleLogout();
      }
      if (e.target.classList.contains('login-button')) {
        this.handleLogin();
      }
      if (e.target.classList.contains('register-button')) {
        this.handleRegister();
      }
    });
  },

  handleClose() {
    if (this.authController && this.authController.getIsAuthenticated()) {
      const user = this.authController.getCurrentUser();
      this.renderSidebar('reg_user', user);
    } else {
      this.renderSidebar('unreg_user');
    }
  },

  handleVisit(e) {
    console.log('Visit button clicked');
    
    if (!this.authController || !this.authController.getIsAuthenticated()) {
      alert('Для адзнакі наведвання месца, калі ласка, увайдзіце ў сістэму');
      return;
    }
    
    // Здесь будет API запрос для отметки посещения
    alert('Месца паспяхова адзначана як наведанае!');
  },

  handleNote(e) {
    console.log('Note button clicked');
    
    if (!this.authController || !this.authController.getIsAuthenticated()) {
      alert('Для дадання нататак, калі ласка, увайдзіце ў сістэму');
      return;
    }
    
    // Здесь будет логика для добавления заметки
    alert('Функцыя дадання нататак будзе даступная ў бліжэйшы час');
  },

  handleLogout() {
    if (this.authController) {
      this.authController.logout();
    }
  },

  handleLogin() {
    if (this.authController) {
      this.authController.showLoginModal();
    }
  },

  handleRegister() {
    if (this.authController) {
      this.authController.showRegisterModal();
    }
  },

  renderSidebar(type, data) {
    const sidebar = document.getElementById("sidebar-content");
    const view = views[type];
    sidebar.innerHTML = view(data);
  }
};