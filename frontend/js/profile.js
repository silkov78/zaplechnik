import { views } from './views.js';

export const sidebarHandlers = {
  init(mapInstance) {
    this.map = mapInstance;
    this.setupEventDelegation();
  },

  setupEventDelegation() {
    const sidebar = document.getElementById('sidebar-content');
    if (!sidebar) return;

    sidebar.addEventListener('click', (e) => {
      if (e.target.id === 'close-sidebar-btn') {
        this.map.flyTo([53.757643, 28.051186], 7)
        this.handleClose();
        this.map.flyTo({
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
    });
  },

  handleClose() {
    //туду 
    //возвращение на профиль если зареганный
    this.renderSidebar('unreg_user');
  },

  handleVisit(e) {
    console.log('Visit button clicked');
    //логика отправки запроса и мб красивые уведы внутри
    alert('Для адзнакі наведвання месца, калі ласка, увайдзіце ў сістэму');
  },

  handleNote(e) {
    console.log('Note button clicked');
    //логика для добавления заметки
    alert('Функцыя дадання нататак будзе даступная ў бліжэйшы час');
  },

  renderSidebar(type, data) {
    const sidebar = document.getElementById("sidebar-content");
    const view = views[type];
    sidebar.innerHTML = view(data);
  }
};