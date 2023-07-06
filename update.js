import fs from 'fs/promises'

const folders = await fs.readdir('src');

const exists = async (filename) => {
    console.log(filename);
    try{
        await fs.stat(filename)
        return true;
    } catch {
        return false;
    }
}

folders.forEach(async (folder) => {
    const metadataFilename = "src/" + folder + "/metadata.json";
    if (!(await exists(metadataFilename))) return;
    
    const {language, implementations} = JSON.parse((await fs.readFile(metadataFilename)).toString());
    console.log(language, implementations);
    // const metadata = JSON.parse("")
    // implementations.push([])
    // console.log(language);
});




// console.log(files);