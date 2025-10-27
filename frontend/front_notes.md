

# Нататки фронта

## Тестовый юзер:

**Логин:** test@example.com  
**Пароль:** password

## Запуск (пока не подрубил докер и тд и тп)

### Локальный сервер
```bash
python -m http.server 8000
```

### Live Server (VS Code)
Установите расширение "Live Server" и запустите через правый клик на index.html

## Запрос к стоянкам (с питчами есть вопросы/ мб будем перемапливать)
```javascript
[out:json][timeout:180];
{{geocodeArea:Belarus}}->.searchArea;

(
  node["tourism"="camp_site"](area.searchArea);
  way["tourism"="camp_site"](area.searchArea);

  node["tourism"="camp_pitch"](area.searchArea);
  relation["tourism"="camp_pitch"](area.searchArea);
);

out center;    
```