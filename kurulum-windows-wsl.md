# Windows 11'de WSL altında Ubuntu 24.0 kurulumu

## WSL tarafı için gerekli kurulumlar

### `Turn Windows features on or off` Ekranında

Aşağıdaki kutucuklar işaretlenir, Tamam düğmesine basılır ve bilgisayar yeniden başlatılır:

- Hyper-V
- Virtual Machine Platform
- Windows Hypervisor Platform
- Windows Subsystem for Linux

Tamam düğmesine basıldığında bilgisayarın yeniden başlatılması gerekir. Yeniden açılma tamamlanana kadar bekleyiniz.

### `Windows Terminal` Ekranında

- Terminal, Yönetici (administrator) yetkisi ile çalıştırılır
- `wsl --update` komutunu çalıştırınız

Bu işlem ile birlikte wsl2 yüklenmiş olacaktır.

### `Microsoft Store` Ekranında

- Microsft Store'da `Ubuntu` yazarak arama yapılır, Ubuntu 24.0 Kurulur.

### Windows Altında Ubuntu'nun Başlatılması

- Uygulamalarda yer alan "Ubuntu 24.04" seçilerek başlatılır.

### Ubuntu'da Kullanıcı Adı ve Parola

- Kurulum sonrası ilk çalıştırmada, ubuntu bizden kullanıcı adı ve parola ister, bunlar ubuntuya giriş yapmayı sağlayan bilgilerdir, saklamayı unutmayın!!!
- `Enter new UNIX username:` buraya kullanıcı adını girin. Örnek: Nuri
- `New password:` buraya parolayı girin. Örnek: 123456
- `Retype new password:` buraya tekrar parolayı girin. Örnek: 123456

### WSL altından Windows dosyalarına erişim

- WSL altından Windows dosyalarına erişim için `/mnt/c` dizini kullanılır.

### Windows altından WSL dosyalarına erişim

- Windows altından WSL dosyalarına erişim için `\\wsl$\Ubuntu-24.04` dizini kullanılır.

## Windows Tarafı için gerekli kurulumlar

### VS Code Kurulumu

- [VS Code Kurulur](https://code.visualstudio.com/download)

### Windsurf Kurulumu

- [Windsurf Kurulur](https://windsurf.com/)
