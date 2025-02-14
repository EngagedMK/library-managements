-- 1. Bảng TaiKhoan (Quản lý thông tin tài khoản và vai trò)
CREATE TABLE TaiKhoan (
    idTaiKhoan INT PRIMARY KEY AUTO_INCREMENT,
    tenDangNhap VARCHAR(255) UNIQUE NOT NULL,
    matKhau VARCHAR(255) NOT NULL,
    vaiTro ENUM('docgia', 'thuthu', 'admin') NOT NULL,
    trangThai ENUM('hoatdong', 'khoa') DEFAULT 'hoatdong'
) ENGINE=InnoDB;

-- 2. Bảng NguoiDung (Quản lý thông tin người dùng)
CREATE TABLE NguoiDung (
    idNguoiDung INT PRIMARY KEY AUTO_INCREMENT,
    hoTen VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    soDienThoai VARCHAR(15),
    idTaiKhoan INT,
    FOREIGN KEY (idTaiKhoan) REFERENCES TaiKhoan(idTaiKhoan) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 3. Bảng KeSach (Quản lý kệ sách)
CREATE TABLE KeSach (
    idKeSach INT PRIMARY KEY AUTO_INCREMENT,
    viTri VARCHAR(255) NOT NULL,
    loaiSach VARCHAR(255) NOT NULL,
    soLuongHienTai INT NOT NULL,
    dungLuongToiDa INT NOT NULL
) ENGINE=InnoDB;

-- 4. Bảng TaiLieu (Quản lý tài liệu)
CREATE TABLE TaiLieu (
    idTaiLieu INT PRIMARY KEY AUTO_INCREMENT,
    tenTaiLieu VARCHAR(255) NOT NULL,
    tacGia VARCHAR(255),
    loaiTaiLieu ENUM('sach', 'tapchi', 'bao') NOT NULL,
    soLuong INT NOT NULL,
    idKeSach INT,
    FOREIGN KEY (idKeSach) REFERENCES KeSach(idKeSach) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 5. Bảng MuonTra (Quản lý mượn trả tài liệu)
CREATE TABLE MuonTra (
    idMuonTra INT PRIMARY KEY AUTO_INCREMENT,
    idNguoiDung INT,
    idTaiLieu INT,
    ngayMuon DATE NOT NULL,
    ngayTra DATE,
    trangThai ENUM('dangmuon', 'datra') NOT NULL,
    FOREIGN KEY (idNguoiDung) REFERENCES NguoiDung(idNguoiDung) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (idTaiLieu) REFERENCES TaiLieu(idTaiLieu) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 6. Bảng LichSuHoatDong (Lưu lịch sử hoạt động của người dùng)
CREATE TABLE LichSuHoatDong (
    idHoatDong INT PRIMARY KEY AUTO_INCREMENT,
    idNguoiDung INT,
    loaiHoatDong VARCHAR(255) NOT NULL,
    thoiGian DATETIME NOT NULL,
    FOREIGN KEY (idNguoiDung) REFERENCES NguoiDung(idNguoiDung) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 7. Bảng Thu (Quản lý các khoản thu)
CREATE TABLE Thu (
    idThu INT PRIMARY KEY AUTO_INCREMENT,
    idNguoiThucHien INT,
    soTien DECIMAL(15, 2) NOT NULL,
    ngayThu DATE NOT NULL,
    moTa TEXT,
    FOREIGN KEY (idNguoiThucHien) REFERENCES NguoiDung(idNguoiDung) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 8. Bảng Chi (Quản lý các khoản chi)
CREATE TABLE Chi (
    idChi INT PRIMARY KEY AUTO_INCREMENT,
    idNguoiThucHien INT,
    soTien DECIMAL(15, 2) NOT NULL,
    ngayChi DATE NOT NULL,
    moTa TEXT,
    FOREIGN KEY (idNguoiThucHien) REFERENCES NguoiDung(idNguoiDung) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;
