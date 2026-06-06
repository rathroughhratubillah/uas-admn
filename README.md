# UAS Cloud Computing II - Ratu Billah Lingga Suci

Identitas:

- Nama: Ratu Billah Lingga Suci
- NIM: 2388010012
- GitHub: https://github.com/rathroughhratubillah
- Docker Hub: https://hub.docker.com/u/ratubillahlinggasuci
- AWS Account: ratubillahlinggasuci (421856467053)
- EC2 Public IPv4: 54.255.60.202

## Arsitektur

Project ini menjalankan dua aplikasi melalui Docker Compose:

- `static-cv`: website CV statis.
- `dynamic-app`: aplikasi dinamis PHP + MariaDB.
- `mariadb`: database untuk aplikasi dinamis.
- `reverse-proxy`: Nginx reverse proxy untuk mengarahkan `/` ke static CV dan `/app` ke dynamic app.

## Docker Image

- Static: `ratubillahlinggasuci/uas-static:latest`
- Dynamic: `ratubillahlinggasuci/uas-dynamic:latest`

## Menjalankan di EC2

Masuk ke EC2:

```bash
ssh -i "uas_2388010012.pem" ubuntu@54.255.60.202
```

Clone repository:

```bash
git clone https://github.com/rathroughhratubillah/uascloud-lingga.git ~/uascloud-lingga
cd ~/uascloud-lingga
cp .env.example .env
docker compose up -d --build
docker compose ps
```

Akses aplikasi:

- Static CV: `http://54.255.60.202`
- Dynamic App: `http://54.255.60.202/app`

Login default dynamic app:

- Username: `admin`
- Password: `admin123`

## Build dan Push Manual

```bash
docker login -u ratubillahlinggasuci
docker compose build static-cv dynamic-app
docker compose push static-cv dynamic-app
```

## GitHub Actions Secrets

Tambahkan secrets berikut di GitHub repository:

- `DOCKERHUB_USERNAME`: `ratubillahlinggasuci`
- `DOCKERHUB_TOKEN`: token Docker Hub
- `EC2_HOST`: `54.255.60.202`
- `EC2_USER`: `ubuntu`
- `EC2_SSH_KEY`: isi private key dari `uas_2388010012.pem`
- `STATIC_IMAGE`: `ratubillahlinggasuci/uas-static:latest`
- `DYNAMIC_IMAGE`: `ratubillahlinggasuci/uas-dynamic:latest`
- `MYSQL_DATABASE`: `uascloud_lingga_db`
- `MYSQL_USER`: `uascloud_lingga_user`
- `MYSQL_ROOT_PASSWORD`: password root database
- `MYSQL_PASSWORD`: password user database
