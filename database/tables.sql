CREATE TABLE Objects (
  object_id INTEGER PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  description VARCHAR(300) NOT NULL,
  extended_description VARCHAR(1000)
);

CREATE TABLE Objects_Images (
  id INTEGER PRIMARY KEY AUTO_INCREMENT,
  object_id INTEGER NOT NULL,
  image_url VARCHAR(500) NOT NULL,
  FOREIGN KEY (object_id) REFERENCES Objects(object_id)
);

CREATE TABLE Tours (
  id INTEGER PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  description VARCHAR(500) NOT NULL
);

CREATE TABLE Tours_Objects (
  tour_id INTEGER NOT NULL,
  position INTEGER NOT NULL,
  object_id INTEGER NOT NULL,
  FOREIGN KEY (tour_id) REFERENCES Tours(id),
  FOREIGN KEY (object_id) REFERENCES Objects(object_id),
  PRIMARY KEY (tour_id, object_id, position)
);

CREATE TABLE Users (
    
  id VARCHAR(36) PRIMARY KEY,
  
  is_guest BOOLEAN DEFAULT 1,

  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Sessions (
  session_id INTEGER PRIMARY KEY AUTO_INCREMENT,
  user_id VARCHAR(36) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  active BOOLEAN DEFAULT 1,
  tour_id INTEGER NOT NULL,
  archetype VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES Users(id),
    FOREIGN KEY (tour_id) REFERENCES Tours(id)
);

CREATE TABLE Sessions_Pictures (
  id INTEGER PRIMARY KEY AUTO_INCREMENT,
  session_id INTEGER NOT NULL,
  image_url VARCHAR(500) NOT NULL,
  FOREIGN KEY (session_id) REFERENCES Sessions(session_id)
);

CREATE TABLE Sessions_Feedback (
  session_id INTEGER NOT NULL PRIMARY KEY,
  feedback_text VARCHAR(255) NOT NULL,
  FOREIGN KEY (session_id) REFERENCES Sessions(session_id)
);

CREATE TABLE Stops (
  session_id INTEGER NOT NULL,
  object_id INTEGER NOT NULL,
  time_of_stop DATETIME DEFAULT CURRENT_TIMESTAMP,
  feedback_text VARCHAR(255),
  PRIMARY KEY (session_id, object_id),
  FOREIGN KEY (session_id) REFERENCES Sessions(session_id),
  FOREIGN KEY (object_id) REFERENCES Objects(object_id)
);