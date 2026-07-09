<?php

test('the package boots in testbench', function () {
    expect($this->app)->toBeInstanceOf(\Illuminate\Foundation\Application::class);
});
