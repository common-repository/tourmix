=== TOURMIX ===
Contributors:           tourmixhungaryltd
Tags:                   WooCommerce, shipping, delivery, dispatch, order tracking
Requires at least:      5.3
Tested up to:           6.6.1
Stable tag:             1.1.6
Requires PHP:           7.2
WC requires at least:   4.5
WC tested up to:        9.1.4
License:                GPL-2.0+
License URI:            http://www.gnu.org/licenses/gpl-2.0.txt

TOURMIX a környezettudatos csomagszállítási alternatíva

== Description ==

Miért ajánljuk a TOURMIX házhozszállítását?
A címzett által megadott időablakban érkeznek a címre, így nem a vásárló alkalmazkodik a futárhoz, hanem épp fordítva.
Decentralizált logisztikai modellüknek köszönhetően a törékeny termékek kiszállítója, nem használnak depókat, így futárjai, a miXerek egyedi módon bánnak a csomagokkal.
Teljes felelősséget vállalnak a csomagokban keletkezett esetleges károkért.
A csomag bármikor és bárhol nyomon követhető a TOURMIX platformban.
Ezzel a szállítási móddal nem csak stresszmentessé tesszük a kézbesítést, hanem a környezetet is kíméljük, hiszen a TOURMIX úgy juttatja el a termékeket, hogy nem keletkeztet plusz járműveket az utakra.


= A plugin használata: =

Miután a plugin-t sikeresen letöltöttük és bekapcsoltuk megjelenik egy TOURMIX almenüpont a WooCommerce menüpont alatt, ahol az összes TOURMIX-os rendelésünket kezelhetjük és könnyedén néhány kattintással át is adhatjuk a TOURMIX rendszerének.
Hozzáférés menete:
* Regisztráció a TOURMIX rendszerébe (https://tourmix.delivery/admin/register)
* Profil adatok kitöltése
* Adatvalidáció (TOURMIX admin végzi, email-ben értesítünk, ha megtörtént)
* Profil fülön az oldal alján található API kulcsot (API tokent) kell megadni a WooCommerce plugin-ban.
Sikeres API Kulcs megadást követően, amint egy új rendelés érkezik, melyet TOURMIX-os szállítási móddal adtak fel, rögtön megjelenik ezen az oldalon és máris kezelhetjük azt.

A beérkező rendeléseket egy táblázatban láthatjuk és kezelhetjük. A táblázat egy adott sor egy adott rendelés adatait mutatja. A Műveletek oszlopban található Feladás gombra kattintva fel tudjuk adni a rendelésünket. Azonban, ha nem egyesével szeretnénk ezt megtenni akkor az adott sorban található jelölőnégyzet segítségével vagy, ha az összes megjelenő rendelést ki szeretnénk választani akkor a táblázat fejlécében található jelölőnégyzetre kattintva ki tudjuk az összes megjelenő rendelést választani. Ezután az Alkalmaz gombra kattintva tudjuk az összes kiválasztott rendelésünket feladni.
A Mit lássunk! gombra kattintva be tudjuk állítani, mely oszlopokat szeretnénk megjeleníteni a táblázatunkban, illetve, hogy egyszerre hány rendelés jelenjen meg mielőtt lapoznunk kellene. Emellett még található egy Felugró ablakok menüpont, melyben a Letöltés opció bejelölésével be tudjuk állítani, hogy a későbbiekben amikor feladunk egy rendelést megjelenjen-e egy felugró ablakban a rendeléshez tartozó TOURMIX rendszer által kiállított címke. Ha nincs bepipálva ez az akkor a Címkék letöltése gombra kattintva bármikor visszakereshetjük a korábban feladott rendelésekhez tartozó címkéket. Végezetül az Alkalmaz gombra kattintva menthetjük a módosításainkat.

A táblázat felett található rendelés állapotra kattintva - például Feladandó - csak az adott státuszú rendeléseket fogjuk látni a listában. (Ez a rendelés státusz nem egyenlő a WooCommerce által kezelt státuszokkal.)

Amikor egy vagy több készpénzes fizetési móddal kiválasztott rendelés feladására kerül sor a Feladás vagy az Alkalmaz gombok valamelyikével, megjelenik egy felugró ablak melyben meg tudjuk adni minden egyes készpénzes rendeléshez a hozzá tartozó számlának a számát. Azonban ez csak opcionális. A felugró ablakban csak a készpénzes rendelésekhez fogja a rendszer kérni a számla számokat.

A WooCommerce > Beállítások > Szállítás > TOURMIX menüpontban tudjuk engedélyezni vagy letiltani a TOURMIX szállítási módot (Engedélyezve van alapértelmezetten). Emellett lehetőségünk vagy Nettó egységár beállítására is, amely meghatározza milyen áron kerüljön felszámolásra a vevőnek a TOURMIX szállítási mód. Azonban az, hogy nettó vagy bruttó ár kerüljön felszámolásra, az ön által meghatározott Adó beállításoktól függ. Végezetül megadható Minimális kosárérték is, ami azt jelenti hogy a megadott érték feletti rendeléseknél nem kerül felszámolásra szállítási költség a vevő részére.

Fontos:
- A telefonszám mezőt kötelezőre kell állítanunk, ugyanis a rendelés kiszállításakor szükség van a vevő telefon számára.
- A bolt cím adatai fontos, hogy mindig ki legyenek töltve, különben, nem fogja tudni a futár, hova menjen a csomagunkért.


Egyéb kérdése esetén keresse bizalommal a TOURMIX ügyfélszolgálatát. (+36209984851)

== Installation ==

1. Töltse le a bővítményt
2. Kapcsolja be a bővítményt
3. WooCommerce / Beállítások / Szállítás / TOURMIX menüben ellenőrizze a beállításokat
4. WooCommerce / TOURMIX menüben található oldalon, első használat előtt, írja be az API kulcsot
5. Ha minden rendben és az API kulcs is helyesen lett megadva, ezen az oldalon fogja tudni kezelni a TOURMIX-hoz kapcsolódó rendeléseket

== Changelog ==

= 1.0.0 =
* Initial version

= 1.0.1 =
* Bug fix

= 1.1.0 =
* Készpénzes rendelések kezelése
* Új Tourmix megoldások használata
* Teljesítmény javítás

= 1.1.2 =
* Készpénzes rendelések számla számát nem kötelező többé megadni
* Dialógus ablakok helyzete innentől a képernyőhöz képest van középre igazítva

= 1.1.3 =
* Súly felkerekítése

== Frequently Asked Questions ==

= Hol találom meg az API kulcsot? =

Az API kulcsot a partner fiókba bejelentkezve a Profil oldalon legörgetve API Token néven lehet megtalálni.

= Mit tegyek ha a plugin nem fogadja el az általam megadott API kulcsot? =

Mindenképp ellenőrizni kell, hogy jól írtuk-e be az API kulcsot ugyanis hibás vagy nem létező API kulcsal nem használható a plugin.

= Ha hibát tapasztalok a plugin működésében kinek szóljak? =

Kerresse fel telefonon vagy emailben a TOURMIX ügyfélszolgálatát.
Telefon: +36204836461
Email: info@tourmix.delivery

== Screenshots ==

1. A plugin aktiválást követően a WooCommerce / TOURMIX menüpont alatt található oldalon az API kulcs bekérő form.
2. Sikeres API kulcs megadás után egy üres oldal fogad minket.
3. A plugin hozzáad egy TOURMIX szállítási módot a meglévő módokon felül.
4. A fentebb említett szállítási móddal rendelt rendelések egy táblázatban kezelhetőek ezen az oldalon.
5. A Feladás vagy Alkalmaz gombok használatával megjelenő ablak, ahhol meg tudjuk adni az utánvétes rendelések számla számát.
6. A jobb felső sarokban található Mit lássunk? gombra kattintáskor lenyíló lista.
7. Rendelés feladást követően megjelenő ablak, melyben a csomagokra ragasztható címkéket találjuk.
8. A Címkék letöltése gombra kattintva egy felugró ablakban a régebben feladott rendelésekhez tartozó címkéket is letölthetjük.
9. A WooCommerce / Beállítások / Szállítás / TOURMIX menüpont alatt további dolgokat is be tudunk állítani.