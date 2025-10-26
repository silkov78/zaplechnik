import json
import requests
import time

INPUT_FILE = 'frontend/data/camp_site.geojson'  
OUTPUT_FILE = 'frontend/data/camp_site_geocoded.geojson' 
USER_AGENT = 'ivannxs7@gmail.com'

def get_location_details(lon, lat, user_agent):

    url = f"https://nominatim.openstreetmap.org/reverse?format=jsonv2&lon={lon}&lat={lat}&accept-language=be"
    
    headers = {
        'User-Agent': user_agent
    }
    
    try:
        response = requests.get(url, headers=headers, timeout=10)
        response.raise_for_status()
        data = response.json()
        
        address = data.get('address', {})
        district = address.get('county') 
        region = address.get('state')   
        
        return district, region
        
    except requests.exceptions.RequestException as e:
        print(f"⚠️ Памылка запыту да Nominatim: {e}")
        return None, None
    except json.JSONDecodeError:
        print("️⚠️ Памылка дэкадавання JSON адказу.")
        return None, None

def main():
    try:
        with open(INPUT_FILE, 'r', encoding='utf-8') as f:
            geojson_data = json.load(f)
    except FileNotFoundError:
        print(f"🚨 Памылка: Файл '{INPUT_FILE}' не знойдзены. "
              f"Пераканайцеся, што ён знаходзіцца ў той жа дырэкторыі, што і скрыпт.")
        return
    except json.JSONDecodeError:
        print(f"🚨 Памылка: Немагчыма прачытаць JSON з файла '{INPUT_FILE}'. "
              f"Праверце, ці правільны ў ім фармат.")
        return

    print(f"✅ Файл '{INPUT_FILE}' паспяхова загружаны.")
    
    features = geojson_data.get('features', [])
    total_features = len(features)
    
    if not features:
        print("Файл не ўтрымлівае ніводнай фічы (features).")
        return

    print(f"Пачынаю апрацоўку {total_features} аб'ектаў...")

    for i, feature in enumerate(features):
        properties = feature.get('properties', {})
        geometry = feature.get('geometry', {})
        
        if geometry and geometry.get('type') == 'Point':
            coordinates = geometry.get('coordinates')
            if not coordinates or len(coordinates) < 2:
                print(f"⏩ Прапускаем аб'ект {i+1}/{total_features}: адсутнічаюць каардынаты.")
                continue

            lon, lat = coordinates
            
            print(f"⚙️  Апрацоўваю аб'ект {i+1}/{total_features} (Каардынаты: {lat}, {lon})...")
            
            district, region = get_location_details(lon, lat, USER_AGENT)
            
            if district:
                properties['district:be'] = district
                print(f"   -> Раён: {district}")
            else:
                print("   -> Не ўдалося вызначыць раён.")

            if region:
                properties['region:be'] = region
                print(f"   -> Вобласць: {region}")
            else:
                print("   -> Не ўдалося вызначыць вобласць.")
            
            time.sleep(1) 
        else:
            print(f"⏩ Прапускаем аб'ект {i+1}/{total_features}: геаметрыя не з'яўляецца пунктам (Point).")

    try:
        with open(OUTPUT_FILE, 'w', encoding='utf-8') as f:
            json.dump(geojson_data, f, ensure_ascii=False, indent=2)
        print(f"\n🎉 Апрацоўка завершана! Вынік захаваны ў файле '{OUTPUT_FILE}'.")
    except IOError:
        print(f"🚨 Памылка: Немагчыма запісаць даныя ў файл '{OUTPUT_FILE}'.")

if __name__ == '__main__':
    main()