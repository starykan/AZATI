SET foreign_key_checks = 0;
CREATE TABLE `people` (
                                  `id` INT NOT NULL AUTO_INCREMENT,
                                  `name` VARCHAR(45) NOT NULL,
                                  `family` VARCHAR(45) NOT NULL,
                                  `patronymic` VARCHAR(45) NOT NULL,
                                  `path` VARCHAR(45) NOT NULL,
                                  `id_speciality` INT NOT NULL ,
                                  PRIMARY KEY (`id`),
                                  CONSTRAINT `people_spetialty`
                                      FOREIGN KEY (`id_speciality`)
                                      REFERENCES `specialities` (`id`)
                                      ON DELETE CASCADE
                                      ON UPDATE CASCADE
                              );


CREATE TABLE `specialities` (
                                     `id` INT NOT NULL AUTO_INCREMENT,
                                     `speciality` VARCHAR(45) NOT NULL,
                                     PRIMARY KEY (`id`));

CREATE TABLE `skill` (
                                 `id` INT NOT NULL AUTO_INCREMENT,
                                 `skill` VARCHAR(200) NOT NULL,
                                  UNIQUE INDEX `SKILLSKILL` (`skill` ASC),
                                 PRIMARY KEY (`id`));

CREATE TABLE `people_skill` (
                                        `id_people` INT NOT NULL,
                                        `id_skill` INT NOT NULL,
                                        CONSTRAINT `people_skill_id_people`
                                            FOREIGN KEY (`id_people`)
                                                REFERENCES `people` (`id`)
                                                ON DELETE CASCADE
                                                ON UPDATE CASCADE,
                                        CONSTRAINT `people_skill_id_skill`
                                            FOREIGN KEY (`id_skill`)
                                                REFERENCES `skill` (`id`)
                                                ON DELETE CASCADE
                                                ON UPDATE CASCADE);

INSERT INTO `specialities` (`speciality`) VALUES ('IT'), ('Marketing'), ('Sales');
SET foreign_key_checks = 1