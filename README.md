# RPO-BitBug

Spletna aplikacija za deljenje datotek in vsebin med uporabniki (slike, videoposnetki, tekst itd.)

## Uporabljene tehnologije

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
docker compose down
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






