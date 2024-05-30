-- Create the Companies table
CREATE TABLE Companies (
    CompanyID INT AUTO_INCREMENT PRIMARY KEY,
    CompanyName VARCHAR(255) NOT NULL,
    CompanyInitials VARCHAR(10)
);

-- Create the Users table
CREATE TABLE Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    FullName VARCHAR(255) NOT NULL,
    Username VARCHAR(50) NOT NULL UNIQUE,
    PasswordHash VARCHAR(255) NOT NULL,
    CompanyID INT,
    FOREIGN KEY (CompanyID) REFERENCES Companies(CompanyID)
);
-- Create the Groups table
CREATE TABLE Groups (
    GroupID INT AUTO_INCREMENT PRIMARY KEY,
    GroupName VARCHAR(255) NOT NULL,
    CompanyID INT,
    FOREIGN KEY (CompanyID) REFERENCES Companies(CompanyID)
);

-- Create the Members table
CREATE TABLE Members (
    MemberID INT AUTO_INCREMENT PRIMARY KEY,
    FullName VARCHAR(255) NOT NULL,
    NationalID VARCHAR(50) NOT NULL,
    Contact VARCHAR(50),
    GroupID INT,
    MemberUniqueID VARCHAR(50) NOT NULL,
    Signature VARCHAR(255),
    DateOfAdmission DATE NOT NULL,
    NextOfKin VARCHAR(255),
    NextOfKinContact VARCHAR(50),
    NextOfKinSignature VARCHAR(255),
    FOREIGN KEY (GroupID) REFERENCES Groups(GroupID)
);

-- Create the Projects table
CREATE TABLE Projects (
    ProjectID INT AUTO_INCREMENT PRIMARY KEY,
    MemberID INT,
    VarietyOfSeedlings VARCHAR(255),
    NumberOfSeedlingsOrdered INT,
    AmountToBePaid DECIMAL(10, 2),
    DepositPaid DECIMAL(10, 2),
    Balance DECIMAL(10, 2),
    DateOfPayment DATE,
    DateToCompletePayment DATE,
    FOREIGN KEY (MemberID) REFERENCES Members(MemberID)
);

-- Create the Areas table
CREATE TABLE Areas (
    AreaID INT AUTO_INCREMENT PRIMARY KEY,
    MemberID INT,
    County VARCHAR(255),
    SubCounty VARCHAR(255),
    Ward VARCHAR(255),
    Location VARCHAR(255),
    SubLocation VARCHAR(255),
    Village VARCHAR(255),
    FOREIGN KEY (MemberID) REFERENCES Members(MemberID)
);

