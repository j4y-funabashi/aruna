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
    id,
    published,
    post_id,
    mention,
    PRIMARY KEY (id)
);
