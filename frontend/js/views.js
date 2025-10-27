//тут лежат все views для sidebar
export const views = {
  unreg_user: (data) => {
    return `
    <div class="welcome-container">
      <h2 class="welcome-title">Zaplechnik</h2>
      <p class="welcome-description">
        Праект "Zaplechnik" ("Заплечнік") ствараецца для таго, каб аб'яднаць супольнасць турыстаў і падарожнікаў, падаўшы ім платформу для сумеснага вядзення і актуалізацыі базы стаянак на тэрыторыі Беларусі. 
        Асноўны фокус зроблены не толькі на зручнасці адзнакі наведаных месцаў, але і на стварэнні жывога кам'юніці, дзе карыстальнікі могуць бачыць прагрэс адзін аднаго, дзяліцца ўражаннямі і спаборнічаць у рамках сістэмы рэйтынгаў.
      </p>
      <div class="auth-buttons">
        <button class="login-button">
          <span class="login-icon">→</span>
          Уваход
        </button>
        <button class="register-button">
          Рэгістрацыя
        </button>
      </div>
    </div>
    `;
  },

  reg_user: (user) => {
    return `
      <div class="user-profile">
        <div class="user-header">
          <div class="user-avatar">
            <img src="${user.avatar || 'https://via.placeholder.com/50'}" alt="Аватар" class="avatar-img">
          </div>
          <div class="user-info">
            <h3 class="user-name">${user.name}</h3>
            <p class="user-email">${user.email}</p>
          </div>
        </div>
        
        <div class="user-stats-simple">
          <div class="stat-line">
            <strong>Telegram:</strong> ${user.telegram || '@username'}
          </div>
          <div class="stat-line">
            <strong>Email:</strong> ${user.email}
          </div>
          <div class="stat-line">
            <strong>Пра сябе:</strong> ${user.about || 'Не указано'}
          </div>
          <div class="stat-line">
            <strong>Дата рэгістрацыі:</strong> ${user.registrationDate || 'Не указано'}
          </div>
          <div class="stat-line">
            <strong>Колькасць наведаных стаянак:</strong> ${user.visitedCamps || 0}
          </div>
        </div>
        
        <div class="user-actions">
          <button class="action-button">
            Статыстыка
          </button>
          <button class="action-button">
            Налады
          </button>
          <button class="logout-button action-button">
            Выйсці
          </button>
        </div>
      </div>
    `;
  },

  camp: (data) => {
    const regionTag = data.properties['region:be'] ? `#${data.properties['region:be'].replace(' вобласць', '').replace(' вобл.', '')}` : '';
    const districtTag = data.properties['district:be'] ? `#${data.properties['district:be'].replace(' раён', '').replace(' р-н', '')}` : '';
    
    return `
    <h2 class="sidebar-title">${data.properties.name || 'Без назвы'}</h2>
    
    <img src="${data.properties.image || 'test_image.png'}" alt="Фото места" class="sidebar-image">
    <p class="sidebar-coords">${data.geometry.coordinates[1].toFixed(6)}, ${data.geometry.coordinates[0].toFixed(6)}</p>
    <button id="close-sidebar-btn">&times;</button>

    <ul class="sidebar-details">
      ${data.properties.website ? `<li><strong>Вэб-сайт:</strong> <a href="${data.properties.website}" target="_blank">тык</a></li>` : ''}
      ${data.properties.fee ? `<li><strong>Інфармацыя пра плату:</strong> ${data.properties.fee === 'yes' ? 'платна' : 'бясплатна'}</li>` : ''}
      ${data.properties.fireplace ? `<li><strong>Абсталяваныя месцы для развядзення агню:</strong> ${data.properties.fireplace === 'yes' ? 'так' : 'не'}</li>` : ''}
      ${data.properties.table ? `<li><strong>Наяўнасць стала:</strong> ${data.properties.picnic_table === 'yes' ? 'так' : 'не'}</li>` : ''}
      ${data.properties.toilet ? `<li><strong>Наяўнасць туалета:</strong> ${data.properties.toilet === 'yes' ? 'так' : 'не'}</li>` : ''}
      ${data.properties.access ? `<li><strong>Даступнасць:</strong> ${data.properties.access}</li>` : ''} 
      ${data.properties.last_update ? `<li><strong>Дата апошняй актуалізацыі дадзеных стаянкі:</strong> ${data.properties.last_update}</li>` : ''}
      ${data.properties.description ? `<li><strong>Апісанне:</strong> ${data.properties.description}</li>` : ''}
      <li><strong>ОSМ:</strong> <a href="https://www.openstreetmap.org/${data.properties['@id']}" target="_blank">лінка</a></li>      
    </ul>

    <div class="hashtags">
      ${regionTag ? `<span class="hashtag">${regionTag}</span>` : ''}
      ${districtTag ? `<span class="hashtag">${districtTag}</span>` : ''}
    </div>

    <div class="action-buttons">
      <button class="note-button">Пакінуць нататку</button>
      <button class="visit-button sidebar-button">Наведаць!</button>
    </div>
    `;
  },
  profile: (data) => {
    return `
      <h3>Профиль</h3>
      <div id="profileChart"></div>
    `;
  },
};