<?php

class  Requests
{
    public function iniciar_sesion()
    {
        return $this->crear_usuario();
    }

    public function crear_usuario()
    {
        return [
            'nombre' => 'Joshua',
            'apellido' => 'Moreno',
            'correo' => 'joshua200128+x@gmail.com',
            'nacimiento' => '1988-08-28',
            'password' => '123456',
            'confirmacion' => '123456',
        ];
    }

    public function post_texto()
    {
        return [
            'texto' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis facilisis mi nec venenatis molestie. Mauris ac est sit amet velit ullamcorper euismod sit amet a urna. Donec nec semper risus. In condimentum turpis et lorem lobortis finibus. Cras egestas congue ante consectetur laoreet. Aliquam quis sodales erat, sit amet gravida nisl. Ut non ipsum vel elit lobortis bibendum et in tellus. Maecenas sit amet est eget eros gravida semper. Interdum et malesuada fames ac ante ipsum primis in faucibus. Vivamus ac dolor fringilla, dapibus ex in, porttitor libero. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec et efficitur eros. Cras blandit arcu id ipsum feugiat laoreet. Mauris turpis mauris, consectetur ac rutrum et, pretium quis velit. Nunc est ligula, pharetra eu elit et, rhoncus lacinia arcu.',
        ];
    }

    public function post_texto_imagen()
    {
        return [
            'texto' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis facilisis mi nec venenatis molestie. Mauris ac est sit amet velit ullamcorper euismod sit amet a urna. Donec nec semper risus. In condimentum turpis et lorem lobortis finibus. Cras egestas congue ante consectetur laoreet. Aliquam quis sodales erat, sit amet gravida nisl. Ut non ipsum vel elit lobortis bibendum et in tellus. Maecenas sit amet est eget eros gravida semper. Interdum et malesuada fames ac ante ipsum primis in faucibus. Vivamus ac dolor fringilla, dapibus ex in, porttitor libero. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec et efficitur eros. Cras blandit arcu id ipsum feugiat laoreet. Mauris turpis mauris, consectetur ac rutrum et, pretium quis velit. Nunc est ligula, pharetra eu elit et, rhoncus lacinia arcu.',
            'imagen' => '/var/www/html/me.jpg',
        ];
    }

    public function post_imagen()
    {
        return [
            'texto' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis facilisis mi nec venenatis molestie. Mauris ac est sit amet velit ullamcorper euismod sit amet a urna. Donec nec semper risus. In condimentum turpis et lorem lobortis finibus. Cras egestas congue ante consectetur laoreet. Aliquam quis sodales erat, sit amet gravida nisl. Ut non ipsum vel elit lobortis bibendum et in tellus. Maecenas sit amet est eget eros gravida semper. Interdum et malesuada fames ac ante ipsum primis in faucibus. Vivamus ac dolor fringilla, dapibus ex in, porttitor libero. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec et efficitur eros. Cras blandit arcu id ipsum feugiat laoreet. Mauris turpis mauris, consectetur ac rutrum et, pretium quis velit. Nunc est ligula, pharetra eu elit et, rhoncus lacinia arcu.',
            'imagen' => '/var/www/html/me.jpg',
        ];
    }
}