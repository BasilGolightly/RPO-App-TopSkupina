CREATE OR REPLACE DATABASE BitBug;
USE BitBug;

-- USER
CREATE OR REPLACE TABLE user(
    id int AUTO_INCREMENT PRIMARY KEY,
    username char(30) NOT NULL,
    password char(255) NOT NULL,
    role enum('user', 'admin', 'mod') NOT NULL DEFAULT 'user',
    joined date DEFAULT CURRENT_DATE()
);

-- LOGIN - logiranje prijav
CREATE OR REPLACE TABLE login(
    id int AUTO_INCREMENT PRIMARY KEY,
    id_user int FOREIGN KEY REFERENCES user(id) NOT NULL,
    logged_in timestamp DEFAULT CURRENT_TIMESTAMP()
);

-- POST - objava
CREATE OR REPLACE TABLE post(
    id int AUTO_INCREMENT PRIMARY KEY,
    id_user int FOREIGN KEY REFERENCES user(id) NOT NULL,
    title char(100) NOT NULL,
    content text NOT NULL
)

-- UPLOAD - objavljena datoteka
CREATE OR REPLACE TABLE upload(
    id int AUTO_INCREMENT PRIMARY KEY,
    id_user int FOREIGN KEY REFERENCES user(id) NOT NULL,
    -- id_post int FOREIGN KEY REFERENCES post(id)
    filename char(100) NOT NULL,
    extension char(10) NOT NULL,
    category enum('picture', 'video', 'code', 'archive') NOT NULL
)

-- POST_UPLOAD - kateremu postu pripadajo datoteke (neobvezna relacija)
CREATE OR REPLACE TABLE post_upload(
    id int AUTO_INCREMENT PRIMARY KEY,
    id_post int FOREIGN KEY REFERENCES post(id) NOT NULL,
    id_upload int FOREIGN KEY REFERENCES upload(id) NOT NULL
)

-- COMMENT - komentarji na poste / threade
CREATE OR REPLACE TABLE comment(
    id int AUTO_INCREMENT PRIMARY KEY,
    id_user int FOREIGN KEY REFERENCES user(id) NOT NULL,
    id_post int FOREIGN KEY REFERENCES post(id) NOT NULL,
    content TEXT NOT NULL,
    id_comment int FOREIGN KEY REFERENCES comment(id) DEFAULT NULL -- za reply-je na drug comment
)









