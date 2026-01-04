DROP DATABASE IF EXISTS BitBug;
CREATE DATABASE BitBug;
USE BitBug;

/*-------------------------USERS-------------------------*/

-- USER
DROP TABLE IF EXISTS users;
CREATE TABLE users(
    id int AUTO_INCREMENT PRIMARY KEY,
    username char(30) NOT NULL,
    password char(255) NOT NULL,
    role enum('user', 'admin', 'mod') NOT NULL DEFAULT 'user',
    joined date DEFAULT CURRENT_DATE,
    description TEXT DEFAULT 'About me =)', 
    /* possible fix, če zgornja koda ne deluje (version dependant):
    joined date DEFAULT (CURRENT_DATE)
    description TEXT */
    privacy enum('private', 'public', 'friends') NOT NULL DEFAULT 'public'
);

-- LOGIN - logiranje prijav
DROP TABLE IF EXISTS login;
CREATE TABLE login(
    id int AUTO_INCREMENT PRIMARY KEY,
    id_user int NOT NULL,
    logged_in timestamp DEFAULT CURRENT_TIMESTAMP(),
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
);

DROP TABLE IF EXISTS follow;
CREATE TABLE follow(
    id int AUTO_INCREMENT PRIMARY KEY,
    id_user1 int NOT NULL,
    id_user2 int NOT NULL,
    accepted tinyint NOT NULL,
    FOREIGN KEY (id_user1) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_user2) REFERENCES users(id) ON DELETE CASCADE
);

/*-------------------------POSTS-------------------------*/

-- POST - objava
DROP TABLE IF EXISTS post;
CREATE TABLE post(
    id int AUTO_INCREMENT PRIMARY KEY,
    id_user int NOT NULL,
    title char(100) NOT NULL,
    content text NOT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
);

-- POST_TAGS - dodajanje tagov na post
DROP TABLE IF EXISTS post_tags;
CREATE TABLE post_tags(
    id int AUTO_INCREMENT PRIMARY KEY,
    tag TEXT NOT NULL,
    id_post int NOT NULL,
    FOREIGN KEY (id_post) REFERENCES post(id) ON DELETE CASCADE
);

-- RATING - ocena posta
DROP TABLE IF EXISTS rating;
CREATE TABLE rating(
    id int AUTO_INCREMENT PRIMARY KEY,
    rating tinyint NOT NULL,
    id_post int NOT NULL,
    FOREIGN KEY (id_post) REFERENCES post(id) ON DELETE CASCADE
);

-- UPLOAD - objavljena datoteka
DROP TABLE IF EXISTS upload;
CREATE TABLE upload(
    id int AUTO_INCREMENT PRIMARY KEY,
    id_user int NOT NULL,
    filename char(100) NOT NULL,
    extension char(10) NOT NULL,
    category enum('picture', 'video', 'code', 'archive') NOT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
);

-- POST_UPLOAD - kateremu postu pripadajo datoteke (neobvezna relacija)
DROP TABLE IF EXISTS post_upload;
CREATE TABLE post_upload(
    id int AUTO_INCREMENT PRIMARY KEY,
    id_post int NOT NULL,
    id_upload int NOT NULL,
    FOREIGN KEY (id_post) REFERENCES post(id) ON DELETE CASCADE,
    FOREIGN KEY (id_upload) REFERENCES upload(id) ON DELETE CASCADE
);

-- PFP - profilske slike (neobvezna relacija med upload in user tabelo)
CREATE TABLE pfp(
    id int AUTO_INCREMENT PRIMARY KEY,
    id_user int NOT NULL,
    id_upload int NOT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_upload) REFERENCES upload(id) ON DELETE CASCADE
);

-- COMMENT - komentarji na poste / threade
DROP TABLE IF EXISTS comment;
CREATE TABLE comment(
    id int AUTO_INCREMENT PRIMARY KEY,
    id_user int NOT NULL,
    id_post int NOT NULL,
    content TEXT NOT NULL,
    id_comment int DEFAULT NULL /* za reply-je na drug comment */,
    FOREIGN KEY (id_comment) REFERENCES comment(id) ON DELETE CASCADE
);

/*-------------------------BOARDS-------------------------*/

-- BOARD
DROP TABLE IF EXISTS board;
CREATE TABLE board(
    id int AUTO_INCREMENT PRIMARY KEY,
    title char(50),
    tag char(50),
    description TEXT
);

-- USER_BOARD 
DROP TABLE IF EXISTS user_board;
CREATE TABLE user_board(
    id int AUTO_INCREMENT PRIMARY KEY,
    id_user int NOT NULL,
    id_board int NOT NULL,
    role enum('admin', 'mod', 'user') NOT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_board) REFERENCES board(id) ON DELETE CASCADE
);

-- DISCUSSION
DROP TABLE IF EXISTS discussion;
CREATE TABLE discussion(
    id int AUTO_INCREMENT PRIMARY KEY,
    title char(100),
    description TEXT,
    id_board int NOT NULL,
    id_user int NOT NULL,
    FOREIGN KEY (id_board) REFERENCES board(id) ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
);

-- USER_DISCUSSION - comment v discussionu
DROP TABLE IF EXISTS user_discussion;
CREATE TABLE user_discussion(
    id int AUTO_INCREMENT PRIMARY KEY,
    id_user int NOT NULL,
    id_discussion int NOT NULL,
    content TEXT NOT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_discussion) REFERENCES discussion(id) ON DELETE CASCADE
);

-- BOARD_POST - post v boardu
DROP TABLE IF EXISTS board_post;
CREATE TABLE board_post(
    id int AUTO_INCREMENT PRIMARY KEY,
    id_board int NOT NULL,
    id_post int NOT NULL,
    FOREIGN KEY (id_board) REFERENCES board(id) ON DELETE CASCADE,
    FOREIGN KEY (id_post) REFERENCES post(id) ON DELETE CASCADE
);

INSERT INTO users(id, username, password, role, joined, description) VALUES (DEFAULT, 'admin', 'root123', 'admin', DEFAULT, DEFAULT);

