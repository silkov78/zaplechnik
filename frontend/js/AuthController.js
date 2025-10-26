export class AuthController {
  constructor() {
    this.currentUser = null;
    this.isAuthenticated = false;
    this.authToken = null;
    this.setupEventListeners();
  }

  setupEventListeners() {
    document.addEventListener('click', (e) => {
      if (e.target.classList.contains('login-button')) {
        this.showLoginModal();
      }
      
      if (e.target.classList.contains('register-button')) {
        this.showRegisterModal();
      }
      
      if (e.target.classList.contains('logout-button')) {
        this.logout();
      }
    });

    document.addEventListener('submit', (e) => {
      if (e.target.id === 'login-form') {
        e.preventDefault();
        this.handleLogin(e.target);
      }
      
      if (e.target.id === 'register-form') {
        e.preventDefault();
        this.handleRegister(e.target);
      }
    });

    document.addEventListener('click', (e) => {
      if (e.target.classList.contains('modal-close') || 
          e.target.classList.contains('modal-overlay')) {
        this.closeModals();
      }
    });
  }

  showLoginModal() {
    const modal = this.createLoginModal();
    document.body.appendChild(modal);
    this.animateModal(modal);
  }

  showRegisterModal() {
    const modal = this.createRegisterModal();
    document.body.appendChild(modal);
    this.animateModal(modal);
  }

  createLoginModal() {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
      <div class="modal-content">
        <div class="modal-header">
          <h3>Уваход</h3>
          <button class="modal-close">&times;</button>
        </div>
        <form id="login-form" class="auth-form">
          <div class="form-group">
            <label for="login-email">Email:</label>
            <input type="email" id="login-email" name="email" required>
          </div>
          <div class="form-group">
            <label for="login-password">Пароль:</label>
            <input type="password" id="login-password" name="password" required>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn-primary">Увайсці</button>
            <button type="button" class="btn-secondary modal-close">Скасаваць</button>
          </div>
        </form>
        <div class="modal-footer">
          <p>Няма акаўнта? <a href="#" class="switch-to-register">Зарэгістравацца</a></p>
        </div>
      </div>
    `;
    return modal;
  }

  createRegisterModal() {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
      <div class="modal-content">
        <div class="modal-header">
          <h3>Рэгістрацыя</h3>
          <button class="modal-close">&times;</button>
        </div>
        <form id="register-form" class="auth-form">
          <div class="form-group">
            <label for="register-name">Імя:</label>
            <input type="text" id="register-name" name="name" required>
          </div>
          <div class="form-group">
            <label for="register-email">Email:</label>
            <input type="email" id="register-email" name="email" required>
          </div>
          <div class="form-group">
            <label for="register-password">Пароль:</label>
            <input type="password" id="register-password" name="password" required minlength="6">
          </div>
          <div class="form-group">
            <label for="register-confirm">Пацвердзіць пароль:</label>
            <input type="password" id="register-confirm" name="confirmPassword" required>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn-primary">Зарэгістравацца</button>
            <button type="button" class="btn-secondary modal-close">Скасаваць</button>
          </div>
        </form>
        <div class="modal-footer">
          <p>Ужо ёсць акаўнт? <a href="#" class="switch-to-login">Увайсці</a></p>
        </div>
      </div>
    `;
    return modal;
  }

  animateModal(modal) {
    modal.style.opacity = '0';
    modal.style.transform = 'scale(0.8)';
    
    requestAnimationFrame(() => {
      modal.style.transition = 'all 0.3s ease';
      modal.style.opacity = '1';
      modal.style.transform = 'scale(1)';
    });
  }

  closeModals() {
    const modals = document.querySelectorAll('.modal-overlay');
    modals.forEach(modal => {
      modal.style.transition = 'all 0.3s ease';
      modal.style.opacity = '0';
      modal.style.transform = 'scale(0.8)';
      
      setTimeout(() => {
        modal.remove();
      }, 300);
    });
  }

  async handleLogin(form) {
    const formData = new FormData(form);
    const credentials = {
      email: formData.get('email'),
      password: formData.get('password')
    };

    try {
      this.showLoading(form);
      
      // Здесь будет реальный API запрос
      const response = await this.authenticateUser(credentials);
      
      if (response.success) {
        this.setUser(response.user, response.token);
        this.closeModals();
        this.notifyAuthChange();
        this.showSuccess('Вы ўвайшлі ў сістэму!');
      } else {
        this.showError(response.message);
      }
    } catch (error) {
      this.showError('Памылка ўваходу. Паспрабуйце яшчэ раз.');
      console.error('Login error:', error);
    } finally {
      this.hideLoading(form);
    }
  }

  async handleRegister(form) {
    const formData = new FormData(form);
    const userData = {
      name: formData.get('name'),
      email: formData.get('email'),
      password: formData.get('password'),
      confirmPassword: formData.get('confirmPassword')
    };

    if (userData.password !== userData.confirmPassword) {
      this.showError('Паролі не супадаюць');
      return;
    }

    if (userData.password.length < 6) {
      this.showError('Пароль павінен быць не менш за 6 сімвалаў');
      return;
    }

    try {
      this.showLoading(form);
      
      // Здесь будет реальный API запрос
      const response = await this.registerUser(userData);
      
      if (response.success) {
        this.setUser(response.user, response.token);
        this.closeModals();
        this.notifyAuthChange();
        this.showSuccess('Вы паспяхова зарэгістраваліся!');
      } else {
        this.showError(response.message);
      }
    } catch (error) {
      this.showError('Памылка рэгістрацыі. Паспрабуйце яшчэ раз.');
      console.error('Register error:', error);
    } finally {
      this.hideLoading(form);
    }
  }

  logout() {
    this.currentUser = null;
    this.isAuthenticated = false;
    this.authToken = null;
    
    localStorage.removeItem('authToken');
    localStorage.removeItem('user');
    
    this.notifyAuthChange();
    this.showSuccess('Вы выйшлі з сістэмы');
  }

  setUser(user, token) {
    this.currentUser = user;
    this.isAuthenticated = true;
    this.authToken = token;
    
    localStorage.setItem('authToken', token);
    localStorage.setItem('user', JSON.stringify(user));
  }

  checkAuthOnLoad() {
    const token = localStorage.getItem('authToken');
    const user = localStorage.getItem('user');
    
    if (token && user) {
      try {
        this.authToken = token;
        this.currentUser = JSON.parse(user);
        this.isAuthenticated = true;
        this.notifyAuthChange();
      } catch (error) {
        console.error('Error parsing user data:', error);
        this.logout();
      }
    }
  }

  notifyAuthChange() {
    const event = new CustomEvent('authChanged', {
      detail: {
        isAuthenticated: this.isAuthenticated,
        user: this.currentUser
      }
    });
    document.dispatchEvent(event);
  }

  // API заглушки
  async authenticateUser(credentials) {
    // Имитация API запроса
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    // Простая проверка для демонстрации
    if (credentials.email === 'test@example.com' && credentials.password === 'password') {
      return {
        success: true,
        user: {
          id: 1,
          name: 'Тестовый пользователь',
          email: credentials.email,
          avatar: 'https://via.placeholder.com/50',
          telegram: '@test',
          about: 'туды - сюды',
          registrationDate: '1 жніўня 2025',
          visitedCamps: 15
        },
        token: 'fake-jwt-token-' + Date.now()
      };
    } else {
      return {
        success: false,
        message: 'Няправільны email або пароль'
      };
    }
  }

  async registerUser(userData) {
    // Имитация API запроса
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    return {
      success: true,
      user: {
        id: Date.now(),
        name: userData.name,
        email: userData.email,
        avatar: 'https://via.placeholder.com/50',
        telegram: '@newuser',
        about: 'Новы карыстальнік',
        registrationDate: new Date().toLocaleDateString('be-BY', { 
          year: 'numeric', 
          month: 'long', 
          day: 'numeric' 
        }),
        visitedCamps: 0
      },
      token: 'fake-jwt-token-' + Date.now()
    };
  }

  showLoading(form) {
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Загрузка...';
  }

  hideLoading(form) {
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = false;
    submitBtn.textContent = submitBtn.textContent.includes('Увайсці') ? 'Увайсці' : 'Зарэгістравацца';
  }

  showError(message) {
    this.showNotification(message, 'error');
  }

  showSuccess(message) {
    this.showNotification(message, 'success');
  }

  showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
      notification.style.opacity = '1';
    }, 100);
    
    setTimeout(() => {
      notification.style.opacity = '0';
      setTimeout(() => notification.remove(), 300);
    }, 3000);
  }

  // Геттеры
  getCurrentUser() {
    return this.currentUser;
  }

  getIsAuthenticated() {
    return this.isAuthenticated;
  }

  getAuthToken() {
    return this.authToken;
  }
}
