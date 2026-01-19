# BitBug

![BitBug](https://github.com/BasilGolightly/RPO-App-TopSkupina/blob/main/app/media/logo1Pixel.png)

Preprosta spletna aplikacija za deljenje datotek in vsebin med uporabniki (slike, videoposnetki, tekst itd.).

## Uporabljene tehnologije in orodja

**MySQL** - DB  
**PHP** - backend  
**HTML, CSS** - front-end  
**Javascript** - dinamičen front-end  
**Docker** - ustvarjanje container-ja  

## Vzpostavitev okolja (Docker)

### Docker
**ZAHTEVA:** Docker Desktop

V terminalu se premaknemo do /container podmape, kjer se nahajajo vse potrebne datoteke za vzpostavitev Docker okolja.

Docker compose združi 3 Docker slike (images):
- php:apache
- mariadb
- phpmyadmin

Da vzpostavimo container zaženemo sledeče ukaze:

```bash
# premakni se v container/
cd container

# Sestavi container z docker compose in zazeni
docker compose up --build

# preveri status
# alternativa: preveri na docker desktop
docker compose ps

# ustavi
docker compose stop

# odstrani
docker compose down -v
```

Po vzpostavljenem okolju lahko dostopamo do posamičnih kontejnerjev preko brskalnika:

```bash

# BitBug - apache
localhost:8080

# PhpMyAdmin
localhost:8081

# MariaDB
localhost:3306

```

## Sodelujoči

### Študenti - RIT 2 UN, FERI, Maribor, 2025/26
- Mark Sadnik (vodja skupine)
- Žiga Bošnjak
- Jan Breznik
- Jan Kavcl
- Eva Kristina Balan
- Elio Štante
- Jaka Zorman

### Mentorja
- asist. Bojan Žlahtič, izvajalec računalniških vaj
- prof. Peter Kokol, nositelj predmeta, izvajalec predavanj

## Dodatne informacije

### Namen 

Prikaz osvojenega znanja na področju skupinskega razvoja programske opreme (predmet RPO - razvoj programske opreme)
in uporabe relevantnih orodij za verzioniranje, beleženje napredka, naslavljanja nalog,
načinim razvoja, skupinskega dela itd. 

### Kje, Kdaj

![BitBug](https://github.com/BasilGolightly/RPO-App-TopSkupina/blob/main/app/media/feri.png)

Univerza v Mariboru, Fakulteta za elektrotehniko, računalništvo in informatiko (FERI),

Program Računalništvo in informacije tehnologije - UN (RIT UN), 2. letnik

Šolsko leto: 2025/26, 1. semester









