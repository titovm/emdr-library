from __future__ import annotations

import json
import re
import unicodedata
from collections import Counter, defaultdict
from pathlib import Path


BASE = Path("outputs/emdr_library_catalog")
records = json.loads((BASE / "inventory.json").read_text(encoding="utf-8"))


def nfc(value: str) -> str:
    return unicodedata.normalize("NFC", value)


for record in records:
    for key in ("relative_path", "filename", "extension"):
        record[key] = nfc(record[key])


ARTICLE_TITLES = {
    "2015 FRONTIERS": "Изменения ЭЭГ в ходе терапии EMDR",
    "A randomized controlled": "EMDR и когнитивно-поведенческая терапия при ОКР: рандомизированное контролируемое исследование",
    "Acarturk": "EMDR при симптомах ПТСР у сирийских беженцев: пилотное рандомизированное исследование",
    "Ahmadi": "Десенсибилизация во время быстрого сна как новый метод терапии ПТСР: рандомизированное исследование",
    "AIP,": "Модель адаптивной переработки информации, выбор мишени и стандартный протокол: стратегии успешной терапии",
    "Bae": "Диссоциация как предиктор ответа на EMDR-терапию при ПТСР",
    "Bandelow": "Эффективность методов лечения тревожных расстройств",
    "Behnammoghadam": "Влияние EMDR на депрессию у пациентов после инфаркта миокарда",
    "Brennstuhl 2015": "EMDR при синдроме фантомной молочной железы: клинический случай",
    "Brennstuhl 2016": "Применение EMDR при хронической боли",
    "Brunnet": "EMDR при ПТСР: систематический обзор",
    "Campagne": "Направляемые движения глаз в терапии травмы: предполагаемые неврологические пути и первые результаты",
    "Carletto": "EMDR при ПТСР у пациентов с рассеянным склерозом: сравнение с релаксационной терапией",
    "Carrick": "Тренировка движений глаз при остром ишемическом инсульте: изменения ЭЭГ и шкалы NIHSS",
    "Chen": "Эффективность EMDR при ПТСР: метаанализ",
    "Chung": "Психологические и фМРТ-характеристики пожарных с частичным ПТСР и эффект EMDR-терапии",
    "Coubard": "Интегративная модель нейронного механизма EMDR",
    "Creating the client": "Создание клиента, способного создавать самого себя",
    "CRITICAL": "Критический анализ современных рекомендаций по лечению комплексного ПТСР",
    "Cusack": "Психологическое лечение ПТСР у взрослых: систематический обзор и метаанализ",
    "d' Ardenne": "Результаты лечения ПТСР по оценке пациентов: психологическая терапия",
    "De Vito": "Движения глаз нарушают эпизодическое представление будущего",
    "Di  Lorenzo": "Изменения ЭЭГ покоя после EMDR-терапии",
    "Di Lorenzo": "Изменения ЭЭГ покоя после EMDR-терапии",
    "Diehle": "Травма-фокусированная КПТ и EMDR у детей с посттравматическим стрессом",
    "Dissociative subtype": "Диссоциативный подтип ПТСР",
    "EMDR and Grief": "EMDR в работе с горем",
    "EMDR vs Stabilisation": "EMDR и обычная стабилизация: сравнительные материалы",
    "Farina": "Нейрофизиологические корреляты сессий EMDR",
    "Franklin": "Эффективность EMDR-терапии при зависимостях",
    "From general": "От общего к частному: выбор целевого воспоминания",
    "Haagen": "Эффективность рекомендованных методов лечения ПТСР у ветеранов: метарегрессионный анализ",
    "Healing the Caregiver": "Исцеление системы заботы с помощью EMDR",
    "Herkt": "Облегчение доступа к эмоциям: нейронная сигнатура стимуляции EMDR",
    "Homer": "Негативные образы при страхе публичных выступлений и нагрузка на зрительно-пространственную рабочую память",
    "Hundt": "Выбор лечения ПТСР: влияние характеристик пациента и терапевта",
    "Influence of dissociative": "Влияние диссоциативного подтипа ПТСР на лечение",
    "Integrating EMDR": "Интеграция EMDR и терапии эго-состояний",
    "Intro to foundations": "Введение в нейробиологические основы EMDR",
    "Jarero": "Интегративный групповой протокол EMDR для детей — жертв тяжелого межличностного насилия",
    "Jowett": "EMDR при ПТСР у взрослых с интеллектуальными нарушениями: клинический обзор",
    "Katzman": "Канадские клинические рекомендации по тревожным расстройствам, ПТСР и ОКР",
    "Kearns": "Движения глаз и образы будущих неудач при страхе публичных выступлений",
    "Lee et al.": "Психотерапия и фармакотерапия ПТСР: систематический обзор методов первой линии",
    "Majidzadeh": "EMDR при депрессии и стрессе у онкологических пациентов",
    "Maroufi": "Влияние EMDR на послеоперационную боль у подростков: рандомизированное исследование",
    "Matzke": "Влияние горизонтальных движений глаз на свободное воспроизведение",
    "McLay": "Эффективность EMDR при военной травме",
    "Merlinda": "Расширенная модель этики в практической работе",
    "Mikhilova": "Применение EMDR у пожилых людей с историей психической травмы",
    "Nijdam": "Ответ на психотерапию ПТСР: роль вербальной памяти до лечения",
    "Novo": "25 лет EMDR: протокол, гипотезы механизмов и обзор эффективности при ПТСР",
    "Oh & Kim": "EMDR при ПТСР у пациентов с биполярным расстройством",
    "Park": "Десенсибилизация триггеров и снижение позыва при парурезе: клинический случай",
    "Pathman": "Как движения глаз помогают понять развитие эпизодической памяти",
    "Paylor": "Оценка эффективности EMDR при сексуальной травме",
    "Perez-Dandieu": "Лечение травмы при зависимости с помощью EMDR: пилотное исследование",
    "PosterDellucci": "Шестиступенчатая модель безопасной EMDR-терапии комплексной травмы",
    "Protocol for Excessive": "Протокол EMDR для работы с чрезмерным горем",
    "Raboni": "Улучшение настроения и сна у пациентов с ПТСР после EMDR-терапии",
    "Ronconi": "Депрессивные симптомы в рандомизированных исследованиях лечения ПТСР: метаанализ",
    "Seliger": "Профессиональная нетрудоспособность вследствие ПТСР",
    "ShapiroAIP": "Модель адаптивной переработки информации Фрэнсин Шапиро",
    "Simiola": "Предпочтения пациентов при выборе терапии травмы: систематический обзор",
    "Specialty topics": "Специальные вопросы применения EMDR с детьми",
    "Staring": "EMDR и COMET для повышения самооценки при тревожных расстройствах",
    "Steenkamp": "Психотерапия ПТСР, связанного с военной службой",
    "Tang": "EMDR для подростков после тайфуна Моракот",
    "Tefft": "EMDR при синдромах хронической боли: обзор литературы",
    "Tsoutsa": "Лечение табачной зависимости с помощью протокола FSAP",
    "Txt of Panic": "Паническое расстройство и агорафобия",
    "Using Eye": "Десенсибилизация зависимости: движения глаз для снижения тяги и интенсивности образов",
    "Wurtz": "Предотвращение устойчивого возвращения страха с помощью билатеральной стимуляции",
    "BPD Journal": "Пограничное расстройство личности и EMDR",
    "BPD, childhood": "Пограничное расстройство личности, детская травма и структурная диссоциация",
    "Complex trauma": "Комплексная травма, диссоциация и пограничное расстройство личности",
    "Reasons for Living": "Опросник причин для жизни",
    "Selfharm": "EMDR в работе с самоповреждением",
}

BOOK_TITLES = {
    "Eye_Movement_Desensitization_and_Reprocessing_EMDR_Scripted_Protocols": "EMDR: сценарные протоколы",
    "Eye_Movement_Desensitization_and_Reprocessing_EMDR_Therapy_Basic": "Основы EMDR-терапии",
    "Eye_Movement_Integration_Therapy_The_Comprehensive_Clinical_Guide": "Терапия интеграции движений глаз: клиническое руководство",
    "Eye_movement_desensitization_and_reprocessing_EMDR_therapy_scripted": "EMDR-терапия: сценарные вмешательства",
    "Handbook_of_EMDR_and_family_therapy": "Руководство по EMDR и семейной терапии",
    "Liz Royle": "Интеграция EMDR в клиническую практику",
    "Книга_EMDR": "EMDR в коучинге",
    "Преодолевая": "Преодолевая прошлое — Фрэнсин Шапиро",
}

SPECIAL_GROUPS = [
    ("Админ/логотипы Ассоциации/", "Логотипы Ассоциации EMDR России и EMDR Europe"),
    ("Диагностика (тесты)/Диагностика Диссоциации (MID)/", "Многофакторный опросник диссоциации (MID): комплект диагностики и интерпретации"),
    ("Диагностика (тесты)/Диссоциация DES/", "Шкала диссоциации DES: бланк, русская версия и ключи"),
    ("Конференции/2 конференция (Москва, 2015)/", "Вторая Российская конференция EMDR, Москва, 2015: комплект материалов"),
    ("Литература (книги и статьи)/Книги/Книги на английском/Нарциссизм_Долорес Маскуэра/", "Нарциссизм и травма — Долорес Москуэра: презентация и статьи"),
    ("Протоколы (открытый доступ)/Работа с ресурсами/", "Работа с ресурсами в EMDR: комплект материалов Анны Коган"),
    ("Семинары и мастерские по темам/EMDR и депрессия/", "EMDR при депрессии: материалы семинара и протокол DeprEnd"),
    ("Семинары и мастерские по темам/EMDR и зависимости/Мастерская_FSAP/", "Протокол зависимого состояния FSAP: запись мастерской и материалы"),
    ("Семинары и мастерские по темам/Нарциссизм_Маскуэра/", "Нарциссизм и травма — Долорес Москуэра: материалы семинара"),
    ("Семинары и мастерские по темам/Структурная диссоциация личности и EMDR/", "Структурная диссоциация личности и EMDR: аудиозапись и ссылка на видео"),
    ("Семинары и мастерские по темам/Тревожные расстройства/", "EMDR при тревожных расстройствах: флешфорвард и материалы семинара"),
    ("Семинары и мастерские по темам/Уди Орен_EMDR как соматический подход/", "EMDR как телесно-ориентированная психотерапия — Уди Орен"),
]


def special_group(path: str):
    for prefix, title in SPECIAL_GROUPS:
        if path.startswith(prefix):
            return prefix, title
    if path.startswith("Конференции/4 конференция (Москва, 2018)/"):
        names = {
            "Сорокина": "EMDR и треугольник Карпмана — Юлия Сорокина",
            "Зотова": "Будущее EMDR — Зотова",
            "Малик": "Тяжелый детский опыт и феномен вспышки — Малик",
            "Градовская": "Самосострадание и EMDR — Надежда Градовская",
            "Пелешок": "Доклад Андрея Пелешка на конференции EMDR 2018",
            "Винник": "Выступление Екатерины Винник на конференции EMDR 2018",
            "Протопопова": "Выступление Е. Г. Протопоповой на конференции EMDR 2018",
            "Волкова": "EMDR при обсессивно-компульсивном расстройстве — Волкова",
            "Лови": "Невидимые травмы — Ольга Лови",
            "Казенная": "Выступление Елены Казенной и Локковой на конференции EMDR 2018",
        }
        for token, title in names.items():
            latin_alias = token == "Зотова" and "zotova" in path.casefold()
            if token.casefold() in path.casefold() or latin_alias or (token == "Казенная" and "Локкова" in path):
                return "2018:" + token, title
        if "Анонсы" in path or "Расписание" in path:
            return "2018:организационные", "Конференция EMDR 2018: анонсы и расписание"
    parent = str(Path(path).parent)
    filename = Path(path).name
    if parent.endswith("Статьи на английском"):
        for prefix in ("Jarero", "EMDR vs Stabilisation", "Di  Lorenzo", "Di Lorenzo", "Using Eye"):
            if filename.startswith(prefix):
                normalized = prefix.replace("  ", " ")
                return parent + ":" + normalized, None
    return path, None


def clean_filename(filename: str) -> str:
    stem = Path(filename).stem
    stem = re.sub(r"[_]+", " ", stem)
    stem = re.sub(r"\s+", " ", stem).strip(" .-_—")
    stem = re.sub(r"\s+(Copy|копия)$", "", stem, flags=re.I)
    return stem


CONF_2019 = {
    "Андрей Пелешок": "В поисках идеального ресурса: возвращение к источнику — Андрей Пелешок",
    "Бардышев": "EMDR-терапия при синдроме хронической тазовой боли — Сергей Бардышев",
    "Градовская": "Некогнитивные переплетения в EMDR: телесно-ориентированные интервенции — Надежда Градовская",
    "Дорош": "Терапевтические отношения при нарушениях привязанности — Елена Дорош",
    "Ерухимович": "EMDR и когнитивно-поведенческая терапия: общность подходов — Ю. А. Ерухимович",
    "Лаврищева": "Интеграция EMDR и транзактного анализа — Алина Лаврищева",
    "Малик": "Адаптация базового протокола EMDR для клиентов с проблемами привязанности",
    "Пелешок Алена": "Как трансформировать терапевтические оплошности в возможности с помощью EMDR — Елена Пелешок",
    "Слободской": "Сочетание EMDR и гипноза — Павел Слободской",
    "Сорокина1": "Работа с «невидимым» флешбеком в EMDR — Юлия Сорокина",
    "Сорокина2": "Техника «Слайд-шоу» — Юлия Сорокина",
}


def title_for(group_title: str | None, group_records: list[dict]) -> str:
    if group_title:
        return group_title
    record = group_records[0]
    path, filename = record["relative_path"], record["filename"]
    if "5 конференция (Питер 2019)" in path:
        for key, title in CONF_2019.items():
            if key in filename:
                return title
    if "Статьи на английском/" in path:
        for prefix, title in ARTICLE_TITLES.items():
            if filename.startswith(prefix):
                return title
    if "/Книги/" in path:
        for prefix, title in BOOK_TITLES.items():
            if filename.startswith(prefix):
                return title
    title = clean_filename(filename)
    replacements = {
        "Blank MID Analysis V3 8": "MID: бланк анализа результатов",
        "Interpreting MID Results": "Интерпретация результатов MID",
        "MID-Print-Version-UPDATED-5-2015": "MID: версия опросника для печати",
        "Flashforward&Mental Video Check Oxford Version DF29-06-24": "Флешфорвард и проверка мысленного видео — Оксфордская версия",
        "Flashforward Oxford Version DF29-06-24": "Протокол «Флешфорвард» — Оксфордская версия",
        "EMDR Happy Childhood ZotovaRev": "EMDR и счастливое детство — Зотова",
        "emdr for si 5 21 17 handouts Kogan": "EMDR при суицидальности: раздаточные материалы — Анна Коган",
        "EMDR фобии2 —": "EMDR при фобиях",
        "Возможности применения EMDR в условиях экстремальной ситуации - короткая версия (1) (1)": "Применение EMDR в условиях экстремальной ситуации — Екатерина Протопопова",
        "Возможности сочетания EMDR и арт-терапии у детей и": "Сочетание EMDR и арт-терапии у детей и подростков — Е. В. Михайлова",
        "Емдр": "EMDR-терапия: адаптивная переработка информации и встреча с Глубинной Самостью — Светлана Сафарова",
        "Преза представление ассоц 2017": "Национальная Ассоциация EMDR: презентация, 2017",
        "Презентация ЕМДР Шцх": "Применение EMDR при шизофрении: возможности и перспективы — Н. Н. Фролова",
        "работа с ранней травмой": "Работа с ранней травмой в EMDR-терапии — Юлия Сорокина",
        "Работа с хронической болью в EMDR - терапии": "Работа с хронической болью в EMDR-терапии",
        "Эмоции": "Интервенции при интенсивных эмоциональных переживаниях",
        "Этика новости EMDR": "Этика и новости EMDR",
        "15 Круглый стол": "Круглый стол конференции EMDR 2018",
        "21 Уди Орен лекция": "Лекция Уди Орена на конференции EMDR 2018",
        "37 Лебедева": "Выступление Лебедевой на конференции EMDR 2018",
        "33. Терапия зависимостей конференция 2021": "EMDR-терапия зависимостей — конференция 2021",
        "34. Части 2021 Бардышев": "Работа с частями личности — Сергей Бардышев, конференция 2021",
        "36. EMDR и роды": "EMDR и роды — конференция 2021",
        "37. Фатеева Работа с домашним насилием NEW": "Работа с домашним насилием — Фатеева, конференция 2021",
        "26. EMDR РПП Отвергнутое я": "EMDR при расстройствах пищевого поведения: «Отвергнутое Я»",
        "cognitive-interweaves-by-d-korn-and-d-laliotis-rus": "Когнитивные переплетения — Д. Корн и Д. Лалиотис",
        "Протокол безмолвных воспоминаний Oxford Version DF20-06-25": "Протокол безмолвных воспоминаний — Оксфордская версия",
        "Флешфорвард Oxford Version DF29-06-24": "Протокол «Флешфорвард» — Оксфордская версия",
        "54-GaryQUINNOK": "Материалы Гэри Куинна по EMDR в чрезвычайных ситуациях",
        "ERP ISP EMDR Казенная Е В 2020": "ERP/ISP: протокол EMDR для чрезвычайных ситуаций — Е. В. Казенная",
        "Self-Care-Procedure-April-12-2020 RUS МК": "Процедура самопомощи EMDR — русская версия, 2020",
        "Казенная Е В Протопопова Е Г 2020 ERP EMDR": "ERP EMDR — Е. В. Казенная и Е. Г. Протопопова, 2020",
        "24. EMDR Borderline Dolores Masquera": "EMDR при пограничном расстройстве личности — Долорес Москуэра",
        "Онно ван дер Харт": "Диссоциация личности и EMDR-терапия сложных травматических расстройств — Онно ван дер Харт",
    }
    return replacements.get(title, title)


def category_for(path: str) -> tuple[str, str]:
    parts = path.split("/")
    top = parts[0]
    cats = {
        "Админ": "Административные материалы",
        "Диагностика (тесты)": "Диагностика и тесты",
        "Конференции": "Конференции",
        "Литература (книги и статьи)": "Литература: книги и статьи",
        "Протоколы (открытый доступ)": "Протоколы (открытый доступ)",
        "Семинары и мастерские по темам": "Семинары и мастерские по темам",
    }
    sub = " / ".join(parts[1:-1])
    return cats.get(top, top), sub


def material_type(paths: list[str], exts: list[str]) -> str:
    text = " ".join(paths).casefold()
    if "логотип" in text:
        return "Графические материалы"
    if "диагност" in text or "опросник" in text or "шкала" in text or "/des/" in text or "/mid/" in text:
        return "Диагностический материал"
    if len(exts) > 1 or len(paths) > 1:
        return "Комплект материалов"
    ext = exts[0]
    if ext in {"mp4", "avi"}: return "Видеозапись"
    if ext == "mp3": return "Аудиозапись"
    if ext in {"ppt", "pptx", "pps"}: return "Презентация"
    if ext in {"doc", "docx", "txt"} and "ссыл" in text: return "Ссылки"
    if "протокол" in text or "procedure" in text: return "Протокол"
    if "/книги/" in text: return "Книга"
    if "/стать" in text: return "Статья"
    if ext in {"doc", "docx", "xls", "xlsx"}: return "Рабочий документ"
    return "Материал"


TAG_RULES = [
    ("ПТСР", ["птср", "ptsd", "ptbs", "posttraumatic", "post-traumatic"]),
    ("Диссоциация", ["диссоц", "dissoci"]),
    ("Комплексная травма", ["комплексн", "complex trauma", "cptsd"]),
    ("Дети и подростки", ["дет", "child", "adolesc", "подрост"]),
    ("Депрессия", ["депресс", "depress"]),
    ("Тревожные расстройства", ["тревож", "anxiety", "panic", "агорафоб", "parures"]),
    ("ОКР", ["окр", "obsessive", "compulsive"]),
    ("Зависимости", ["зависим", "addict", "craving", "tobacco", "курен"]),
    ("Хроническая боль", ["хроническ", "chronic pain", "тазовой боли", "phantom", "pain syndrome"]),
    ("Утрата и горе", ["горе", "grief"]),
    ("Привязанность", ["привязан", "attachment", "caregiver"]),
    ("Нейробиология", ["нейро", "eeg", "fmri", "neural", "memory", "памят"]),
    ("Исследования эффективности", ["эффектив", "efficacy", "effectiveness", "randomized", "meta-analysis", "метаанализ", "systematic review", "систематическ"]),
    ("Стабилизация и ресурсы", ["стабилиз", "ресурс", "resource", "cipos", "контейнер"]),
    ("Групповая работа", ["группов", "group treatment"]),
    ("ЧС и недавняя травма", ["чс", "недавн", "r-tep", "erp", "refugee", "тайфун", "пожарн"]),
    ("Самопомощь", ["самопом", "self-care"]),
    ("Личностные расстройства", ["погранич", "borderline", "bpd", "нарцисс"]),
    ("Телесно-ориентированная работа", ["телес", "body oriented", "соматическ"]),
    ("Семья", ["семейн", "family"]),
    ("Военная травма", ["военн", "military", "veteran"]),
    ("Сексуальная травма", ["sexual trauma", "сексуальн", "rape"]),
    ("Коучинг", ["коучинг", "coaching"]),
    ("Этика", ["этик", "ethic"]),
    ("РПП", ["рпп", "пищев"]),
    ("Самоповреждение", ["самоповреж", "selfharm", "self-harm"]),
    ("Суицидальность", ["суицид", "suicid", "reasons for living"]),
    ("Когнитивные переплетения", ["переплет", "interweav"]),
    ("Гипноз", ["гипноз", "hypno"]),
    ("Травма раннего опыта", ["ранн", "childhood trauma", "early trauma"]),
    ("Домашнее насилие", ["домашним насилием", "domestic violence"]),
    ("Роды и перинатальная травма", ["роды", "birth"]),
]


def tags_for(title: str, paths: list[str]) -> list[str]:
    hay = (title + " " + " ".join(paths)).casefold()
    tags = []
    for tag, needles in TAG_RULES:
        if any(n.casefold() in hay for n in needles):
            tags.append(tag)
    return tags[:3] or ["EMDR"]


groups = defaultdict(lambda: {"title": None, "records": []})
for record in records:
    key, group_title = special_group(record["relative_path"])
    groups[key]["records"].append(record)
    groups[key]["title"] = groups[key]["title"] or group_title

hash_counts = Counter(r["sha256"] for r in records)
catalog = []
file_rows = []
for index, (_, group) in enumerate(sorted(groups.items(), key=lambda kv: min(r["relative_path"] for r in kv[1]["records"]).casefold()), 1):
    rs = sorted(group["records"], key=lambda r: r["relative_path"].casefold())
    paths = [r["relative_path"] for r in rs]
    exts = sorted(set(r["extension"].upper() or "БЕЗ РАСШИРЕНИЯ" for r in rs))
    title = title_for(group["title"], rs)
    category, subcategory = category_for(paths[0])
    duplicate_count = sum(1 for r in rs if hash_counts[r["sha256"]] > 1)
    notes = []
    if duplicate_count:
        notes.append(f"Есть точные копии: {duplicate_count} файл(а/ов)")
    if any("(1)" in r["filename"] or "— копия" in r["filename"].casefold() or " Copy" in r["filename"] for r in rs):
        notes.append("Проверьте версии/копии перед публикацией")
    rid = f"EMDR-{index:03d}"
    catalog.append({
        "id": rid,
        "title": title,
        "category": category,
        "subcategory": subcategory,
        "type": material_type(paths, [e.casefold() for e in exts]),
        "tags": ", ".join(tags_for(title, paths)),
        "language": "английский" if "на английском" in paths[0].casefold() else ("не применимо" if category == "Административные материалы" else "русский"),
        "year": next((int(y) for y in re.findall(r"(?:19|20)\d{2}", title + " " + paths[0]) if 1900 <= int(y) <= 2030), None),
        "file_count": len(rs),
        "formats": ", ".join(exts),
        "size_bytes": sum(r["size_bytes"] for r in rs),
        "files": "\n".join(r["filename"] for r in rs),
        "paths": "\n".join(paths),
        "notes": "; ".join(notes),
    })
    for r in rs:
        file_rows.append({
            "resource_id": rid,
            "title": title,
            "filename": r["filename"],
            "path": r["relative_path"],
            "format": r["extension"].upper(),
            "size_bytes": r["size_bytes"],
            "exact_duplicate": "Да" if hash_counts[r["sha256"]] > 1 else "Нет",
            "fingerprint": r["sha256"][:12],
        })

summary = {
    "source_folder": "/Volumes/Video Drive/Temp/Библиотека Ассоциации EMDR России/",
    "physical_files": len(records),
    "catalog_entries": len(catalog),
    "total_bytes": sum(r["size_bytes"] for r in records),
    "duplicate_files": sum(1 for r in records if hash_counts[r["sha256"]] > 1),
    "categories": dict(Counter(row["category"] for row in catalog)),
    "types": dict(Counter(row["type"] for row in catalog)),
    "tags": dict(Counter(t.strip() for row in catalog for t in row["tags"].split(","))),
}

(BASE / "catalog.json").write_text(json.dumps(catalog, ensure_ascii=False, indent=2), encoding="utf-8")
(BASE / "files.json").write_text(json.dumps(file_rows, ensure_ascii=False, indent=2), encoding="utf-8")
(BASE / "summary.json").write_text(json.dumps(summary, ensure_ascii=False, indent=2), encoding="utf-8")
print(json.dumps(summary, ensure_ascii=False))
