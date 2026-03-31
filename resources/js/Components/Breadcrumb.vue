<template>
    <ul id="breadcrumbs" class="list-none m-0 p-0">
        <li>
            <Link href="/"><i class="fa-solid fa-house"></i></Link>
        </li>
        <li>
            <a class="current">
                {{ name }}
            </a>
        </li>
    </ul>
</template>
<script setup>
import { Link } from "@inertiajs/vue3";
defineProps(["name"]);
</script>
<style scoped>
#breadcrumbs {
    overflow: hidden;
    width: 100%;
    font-size: 24px;
    font-weight: 700;
}

#breadcrumbs li {
    float: left;
    margin: 0 15px 0 35px;
}

#breadcrumbs a {
    background: #d9d9d9;
    padding: 2px 8px;
    float: left;
    text-decoration: none;
    color: #444;
    position: relative;
    height: 40px;
    min-width: 75px;
    text-align: center;
}

#breadcrumbs a:hover {
    background: #f58220;
}

#breadcrumbs a::before {
    content: "";
    position: absolute;
    top: 50%;
    margin-top: -20px;
    border-width: 20px 0 20px 40px;
    border-style: solid;
    border-color: #d9d9d9 #d9d9d9 #d9d9d9 transparent;
    left: -40px;
}

#breadcrumbs a:hover::before {
    border-color: #f58220 #f58220 #f58220 transparent;
}

#breadcrumbs a::after {
    content: "";
    position: absolute;
    top: 50%;
    margin-top: -20px;
    border-top: 20px solid transparent;
    border-bottom: 20px solid transparent;
    border-left: 40px solid #d9d9d9;
    right: -40px;
}

#breadcrumbs a:hover::after {
    border-left-color: #f58220;
}

#breadcrumbs .current {
    background: #f58220;
    padding: 2px 8px;
    float: left;
    text-decoration: none;
    color: #ffffff;
    position: relative;
    height: 40px;
    pointer-events: none;
}

#breadcrumbs .current::before {
    content: "";
    position: absolute;
    top: 50%;
    margin-top: -20px;
    border-width: 20px 0 20px 40px;
    border-style: solid;
    border-color: #f58220 #f58220 #f58220 transparent;
    left: -40px;
}

#breadcrumbs .current::after {
    content: "";
    position: absolute;
    top: 50%;
    margin-top: -20px;
    border-top: 20px solid transparent;
    border-bottom: 20px solid transparent;
    border-left: 40px solid #f58220;
    right: -40px;
}
</style>
