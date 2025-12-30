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
            <img src="${user.avatarUrl || 'https://via.placeholder.com/50'}" alt="Аватар" class="avatar-img">
          </div>
          <div class="user-info">
            <h3 class="user-name">${user.name}</h3>
            <p class="user-email">${user.email}</p>
          </div>
        </div>
        
        <div class="user-stats-simple">
          <div class="stat-line">
            <strong>Telegram:</strong> ${user.telegram || 'Не пазначана'}
          </div>
          <div class="stat-line">
            <strong>Пра сябе:</strong> ${user.bio || 'Не пазначана'}
          </div>
          <div class="stat-line">
            <strong>Колькасць наведаных стаянак:</strong> ${user.info.visits_count || 'Не пазначана'}
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
    console.log(data.properties);

    const regionTag = data.properties.script_region ? `#${data.properties.script_region.replace(' вобласць', '').replace(' вобл.', '')}` : '';
    const districtTag = data.properties.script_district ? `#${data.properties.script_district.replace(' раён', '').replace(' р-н', '')}` : '';
    
    return `
    <h2 class="sidebar-title">${data.properties.osm_name || 'Без назвы'}</h2>
    
    <img src="${data.properties.osm_image || 'test_image.png'}" alt="Фото места" class="sidebar-image">
    <p class="sidebar-coords">${data.geometry.coordinates[1].toFixed(6)}, ${data.geometry.coordinates[0].toFixed(6)}</p>
    <button id="close-sidebar-btn">&times;</button>

    <ul class="sidebar-details">
      ${data.properties.osm_website ? `<li><strong>Вэб-сайт:</strong> <a href="${data.properties.osm_website}" target="_blank">тык</a></li>` : ''}
      ${data.properties.osm_fee ? `<li><strong>Інфармацыя пра плату:</strong> ${data.properties.osm_fee === 'yes' ? 'платна' : 'бясплатна'}</li>` : ''}
      ${data.properties.osm_fireplace ? `<li><strong>Абсталяваныя месцы для развядзення агню:</strong> ${data.properties.osm_fireplace === 'yes' ? 'так' : 'не'}</li>` : ''}
      ${data.properties.osm_table ? `<li><strong>Наяўнасць стала:</strong> ${data.properties.osm_picnic_table === 'yes' ? 'так' : 'не'}</li>` : ''}
      ${data.properties.osm_toilet ? `<li><strong>Наяўнасць туалета:</strong> ${data.properties.osm_osm_toilet === 'yes' ? 'так' : 'не'}</li>` : ''}
      ${data.properties.osm_access ? `<li><strong>Даступнасць:</strong> ${data.properties.osm_access}</li>` : ''} 
      ${data.properties.osm_last_update ? `<li><strong>Дата апошняй актуалізацыі дадзеных стаянкі:</strong> ${data.properties.osm_last_update}</li>` : ''}
      ${data.properties.osm_description ? `<li><strong>Апісанне:</strong> ${data.properties.osm_description}</li>` : ''}
      <li><strong>ОSМ:</strong> <a href="https://www.openstreetmap.org/${data.properties.osm_id}" target="_blank">лінка</a></li>      
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