<?php
declare(strict_types=1);

function health_language(string $language=''): string
{
    $language=$language?:site_language();
    return in_array($language,['rw','en','fr'],true)?$language:'rw';
}

function health_topics(): array
{
    return [
        ['slug'=>'general-health','icon'=>'✚','name'=>['rw'=>'Ubuzima Rusange','en'=>'General Health','fr'=>'Santé générale'],'summary'=>['rw'=>'Amakuru yizewe ku kwita ku buzima no kwirinda indwara.','en'=>'Reviewed education on everyday health and prevention.','fr'=>'Informations révisées sur la santé et la prévention.']],
        ['slug'=>'hiv-aids','icon'=>'🎗','name'=>['rw'=>'VIH/SIDA','en'=>'HIV/AIDS','fr'=>'VIH/SIDA'],'summary'=>['rw'=>'Gukumira, kwisuzumisha no kubona serivisi zemewe.','en'=>'Prevention, testing and access to qualified services.','fr'=>'Prévention, dépistage et services qualifiés.']],
        ['slug'=>'malaria','icon'=>'🦟','name'=>['rw'=>'Malariya','en'=>'Malaria','fr'=>'Paludisme'],'summary'=>['rw'=>'Kwirinda no kumenya igihe cyo gushaka ubufasha kwa muganga.','en'=>'Prevention and recognizing when professional care is needed.','fr'=>'Prévention et recours approprié aux soins.']],
        ['slug'=>'cancer','icon'=>'◉','name'=>['rw'=>'Kanseri','en'=>'Cancer','fr'=>'Cancer'],'summary'=>['rw'=>'Ubumenyi rusange ku gusuzuma hakiri kare no kwivuza.','en'=>'General education on screening, early detection and care.','fr'=>'Information générale sur le dépistage et les soins.']],
        ['slug'=>'diabetes','icon'=>'◒','name'=>['rw'=>'Diyabete','en'=>'Diabetes','fr'=>'Diabète'],'summary'=>['rw'=>'Kumenya ibyago, kwisuzumisha no gukurikiranwa n’abaganga.','en'=>'Risk awareness, screening and clinician-led follow-up.','fr'=>'Risques, dépistage et suivi clinique.']],
        ['slug'=>'heart-diseases','icon'=>'♥','name'=>['rw'=>'Indwara z’Umutima','en'=>'Heart Diseases','fr'=>'Maladies cardiaques'],'summary'=>['rw'=>'Amakuru ku mutima, umuvuduko w’amaraso no kwirinda.','en'=>'Heart health, blood pressure and prevention education.','fr'=>'Santé cardiaque, tension artérielle et prévention.']],
        ['slug'=>'maternal-child-health','icon'=>'⚕','name'=>['rw'=>'Ubuzima bw’Ababyeyi n’Abana','en'=>'Maternal & Child Health','fr'=>'Santé maternelle et infantile'],'summary'=>['rw'=>'Ubumenyi bw’ababyeyi, inda, kubyara n’imikurire y’umwana.','en'=>'Education on pregnancy, birth, parenting and child growth.','fr'=>'Grossesse, naissance, parentalité et croissance.']],
        ['slug'=>'mental-health','icon'=>'☼','name'=>['rw'=>'Ubuzima bwo mu Mutwe','en'=>'Mental Health','fr'=>'Santé mentale'],'summary'=>['rw'=>'Ubumenyi, kwirinda akato no gushaka ubufasha bukwiye.','en'=>'Education, stigma reduction and routes to qualified support.','fr'=>'Information, lutte contre la stigmatisation et soutien.']],
        ['slug'=>'nutrition','icon'=>'●','name'=>['rw'=>'Imirire','en'=>'Nutrition','fr'=>'Nutrition'],'summary'=>['rw'=>'Imirire iboneye, amazi n’umutekano w’ibiribwa.','en'=>'Balanced nutrition, hydration and food safety.','fr'=>'Alimentation équilibrée, hydratation et sécurité alimentaire.']],
        ['slug'=>'sexual-reproductive-health','icon'=>'∞','name'=>['rw'=>'Ubuzima bw’Imyororokere','en'=>'Sexual & Reproductive Health','fr'=>'Santé sexuelle et reproductive'],'summary'=>['rw'=>'Amakuru yubaha umuntu kandi ashingiye ku bimenyetso.','en'=>'Respectful, evidence-informed health education.','fr'=>'Éducation respectueuse fondée sur les données.']],
        ['slug'=>'infectious-diseases','icon'=>'⬡','name'=>['rw'=>'Indwara Zandura','en'=>'Infectious Diseases','fr'=>'Maladies infectieuses'],'summary'=>['rw'=>'Isuku, inkingo, kwirinda no gukoresha serivisi z’ubuzima.','en'=>'Hygiene, vaccination, prevention and health services.','fr'=>'Hygiène, vaccination, prévention et services de santé.']],
    ];
}

function health_tips(): array
{
    return [
        ['slug'=>'healthy-nutrition','icon'=>'🍎','name'=>['rw'=>'Imirire Iboneye','en'=>'Healthy Nutrition','fr'=>'Nutrition saine'],'summary'=>['rw'=>'Amafunguro yuzuye, amazi n’umutekano w’ibiribwa.','en'=>'Balanced meals, hydration and food safety.','fr'=>'Repas équilibrés, hydratation et sécurité alimentaire.']],
        ['slug'=>'physical-activity','icon'=>'🏃','name'=>['rw'=>'Siporo n’Imyitozo','en'=>'Physical Activity','fr'=>'Activité physique'],'summary'=>['rw'=>'Kugenda, kwiruka n’imyitozo ikorwa mu mutekano.','en'=>'Walking, running and age-appropriate safe movement.','fr'=>'Marche, course et mouvement adapté.']],
        ['slug'=>'hygiene-sanitation','icon'=>'💧','name'=>['rw'=>'Isuku n’Isukura','en'=>'Hygiene & Sanitation','fr'=>'Hygiène et assainissement'],'summary'=>['rw'=>'Gukaraba intoki, amazi meza n’isuku y’ibiribwa.','en'=>'Hand hygiene, safe water and food hygiene.','fr'=>'Hygiène des mains, eau potable et aliments.']],
        ['slug'=>'healthy-sleep','icon'=>'☾','name'=>['rw'=>'Gusinzira Neza','en'=>'Healthy Sleep','fr'=>'Sommeil sain'],'summary'=>['rw'=>'Gahunda yo gusinzira n’ahantu hatekanye ho kuruhukira.','en'=>'Sleep routines and a supportive sleep environment.','fr'=>'Routines et environnement favorables au sommeil.']],
        ['slug'=>'disease-prevention','icon'=>'🛡','name'=>['rw'=>'Kwirinda Indwara','en'=>'Disease Prevention','fr'=>'Prévention des maladies'],'summary'=>['rw'=>'Inkingo, kwisuzumisha no kumenya serivisi zikwiye.','en'=>'Vaccination awareness, screening and appropriate services.','fr'=>'Vaccination, dépistage et services appropriés.']],
        ['slug'=>'family-health','icon'=>'👪','name'=>['rw'=>'Ubuzima mu Muryango','en'=>'Family Health','fr'=>'Santé familiale'],'summary'=>['rw'=>'Ubuzima bw’umuryango, uburere n’imibereho myiza.','en'=>'Family wellness, parenting and emotional well-being.','fr'=>'Bien-être familial, parentalité et santé émotionnelle.']],
    ];
}

function health_tools(): array
{
    return [
        ['slug'=>'bmi-calculator.php','icon'=>'▦','name'=>['rw'=>'Kubara BMI','en'=>'BMI Calculator','fr'=>'Calculateur IMC'],'summary'=>['rw'=>'Igipimo rusange cy’abakuru gishingiye ku burebure n’ibiro.','en'=>'An adult screening measure using height and weight.','fr'=>'Un indicateur de dépistage pour adultes.']],
        ['slug'=>'pregnancy-calculator.php','icon'=>'◕','name'=>['rw'=>'Kubara Igihe cyo Kubyara','en'=>'Pregnancy Calculator','fr'=>'Calcul de grossesse'],'summary'=>['rw'=>'Igihe giteganyijwe gishingiye ku mihango ya nyuma.','en'=>'An estimated timeline based on the last menstrual period.','fr'=>'Calendrier estimatif basé sur les dernières règles.']],
        ['slug'=>'child-growth-tracker.php','icon'=>'↗','name'=>['rw'=>'Gukurikirana Imikurire','en'=>'Child Growth Tracker','fr'=>'Suivi de croissance'],'summary'=>['rw'=>'Kugereranya ibiro n’imyaka n’igipimo cya WHO 0–5.','en'=>'Compare weight-for-age with the WHO 0–5 standard.','fr'=>'Comparer le poids-pour-l’âge avec la norme OMS 0–5.']],
    ];
}

function health_item_by_slug(array $items,string $slug): ?array
{
    foreach($items as $item)if($item['slug']===$slug)return $item;
    return null;
}

function health_text(array $localized,string $language): string
{
    $language=health_language($language);
    return (string)($localized[$language]??$localized['rw']??reset($localized));
}
