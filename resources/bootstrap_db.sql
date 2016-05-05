DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS mentions;

CREATE TABLE IF NOT EXISTS posts (
    id,
    published,
    type,
    post,
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS mentions (
    uid,
    published,
    post_uid,
    author_name,
    author_photo,
    author_url,
    is_like,
    is_comment,
    content,
    PRIMARY KEY (uid)
);
